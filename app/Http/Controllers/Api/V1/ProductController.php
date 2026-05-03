<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariationItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private const HOTEL_VILLA_DATE_ATTRIBUTE_SET_SLUG = 'product-date-hotelvilla';

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        $keyword = trim((string) $request->get('keyword', ''));
        $type = trim((string) $request->get('type', ''));

        if ($perPage <= 0) {
            $perPage = 10;
        }

        if ($perPage > 50) {
            $perPage = 50;
        }

        if ($page <= 0) {
            $page = 1;
        }

        $query = DB::table('ec_products as p')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'p.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
            })
            ->where('p.is_variation', 0)
            ->where('p.status', 'published')
            ->select([
                'p.id',
                'p.name',
                'p.sku',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.thumbnail',
                'p.images',
                'p.description',
                'p.content',
                'p.address',
                'p.type',
                'p.level_star',
                'p.status',
                'p.created_at',
                's.key as slug',
            ]);

        if ($keyword !== '') {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('p.name', 'like', "%{$keyword}%")
                    ->orWhere('p.description', 'like', "%{$keyword}%")
                    ->orWhere('p.content', 'like', "%{$keyword}%")
                    ->orWhere('p.address', 'like', "%{$keyword}%")
                    ->orWhere('s.key', 'like', "%{$keyword}%");
            });
        }

        if ($type !== '') {
            $query->where('p.type', mb_strtolower($type));
        }

        $total = (clone $query)->count('p.id');

        $items = $query
            ->orderByDesc('p.id')
            ->forPage($page, $perPage)
            ->get()
            ->map(fn($item) => $this->transformListItem($item))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully.',
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $product = $this->findProductBySlug($slug);

            if (! $product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            $raw = DB::table('ec_products as p')
                ->leftJoin('slugs as s', function ($join) {
                    $join->on('s.reference_id', '=', 'p.id')
                        ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
                })
                ->where('p.id', $product->id)
                ->select([
                    'p.id',
                    'p.name',
                    'p.sku',
                    'p.price',
                    'p.sale_price',
                    'p.image',
                    'p.thumbnail',
                    'p.images',
                    'p.description',
                    'p.content',
                    'p.address',
                    'p.type',
                    'p.level_star',
                    'p.status',
                    'p.created_at',
                    'p.views',
                    'p.is_featured',
                    's.key as slug',
                ])
                ->first();

            if (! $raw) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            $gallery = $this->parseGallery($raw->images ?? null);
            $meta = $this->getProductMeta((int) $product->id);

            $thumbnail = $this->resolveThumbnail(
                $raw->thumbnail ?? null,
                $raw->image ?? null,
                $gallery,
            );

            $heroImages = collect([
                $thumbnail,
                $this->formatImageUrl($raw->image ?? null),
            ])->merge($gallery)
                ->filter()
                ->unique()
                ->values()
                ->all();

            $categories = DB::table('ec_product_category_product as pcp')
                ->join('ec_product_categories as c', 'c.id', '=', 'pcp.category_id')
                ->leftJoin('slugs as s', function ($join) {
                    $join->on('s.reference_id', '=', 'c.id')
                        ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\ProductCategory');
                })
                ->where('pcp.product_id', $product->id)
                ->where('c.status', 'published')
                ->select([
                    'c.id',
                    'c.name',
                    'c.description',
                    'c.image',
                    'c.icon_image',
                    'c.icon',
                    's.key as slug',
                ])
                ->orderByDesc('c.id')
                ->get()
                ->map(fn($item) => [
                    'id' => (int) $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'description' => $item->description,
                    'image' => $this->formatImageUrl($item->image ?: $item->icon_image ?: $item->icon),
                    'icon' => $this->formatImageUrl($item->icon_image ?: $item->icon ?: $item->image),
                ])
                ->unique('id')
                ->values()
                ->all();

            $extensions = collect($product->extensions ?? [])
                ->map(function ($item) {
                    return [
                        'id' => (int) $item->id,
                        'name' => $item->name ?? '',
                        'image' => $this->formatImageUrl($item->image ?? null),
                    ];
                })
                ->values()
                ->all();

            $services = collect($product->services ?? [])
                ->map(function ($item) {
                    $price = (float) ($item->price ?? 0);

                    return [
                        'id' => (int) $item->id,
                        'name' => $item->name ?? '',
                        'price' => $price,
                        'price_label' => $this->formatMoney($price),
                        'image' => $this->formatImageUrl($item->image ?? null),
                    ];
                })
                ->values()
                ->all();

            $groupedRoomsCollection = $this->getGroupedRoomsForCalendar((int) $product->id);

            $groupedRooms = $groupedRoomsCollection
                ->map(function ($room) {
                    $roomMeta = $this->getProductMeta((int) $room->id);
                    $roomGallery = $this->parseGallery($room->images ?? null);
                    $roomThumb = $this->resolveThumbnail(
                        $room->thumbnail ?? null,
                        $room->image ?? null,
                        $roomGallery,
                    );

                    $basePrice = (float) (($room->sale_price ?: $room->price) ?? 0);
                    $roomCalendar = $this->getCalendarData((int) $room->id, $basePrice);

                    return [
                        'id' => (int) $room->id,
                        'name' => $room->name ?? '',
                        'slug' => optional($room->slugable)->key,
                        'description' => $this->cleanHtmlText($room->description ?? ''),
                        'image' => $roomThumb,
                        'price_value' => $basePrice,
                        'price' => $this->formatMoney($basePrice),
                        'max_adults' => (int) $this->decodeMetaScalar($roomMeta['max_adults'] ?? 0, 0),
                        'max_children' => (int) $this->decodeMetaScalar($roomMeta['max_children'] ?? 0, 0),
                        'beds' => (int) $this->decodeMetaScalar($roomMeta['beds'] ?? 0, 0),
                        'size' => $this->decodeMetaScalar($roomMeta['size'] ?? null),
                        'children_surplus_fee' => (float) $this->decodeMetaScalar($roomMeta['children_surplus_fee'] ?? 0, 0),
                        'addon_fee' => (float) $this->decodeMetaScalar($roomMeta['addonFee'] ?? 0, 0),
                        'service_fee' => (float) $this->decodeMetaScalar($roomMeta['serviceFee'] ?? 0, 0),
                        'available_dates' => $roomCalendar['available_dates'],
                        'booked_dates' => $roomCalendar['booked_dates'],
                        'calendar_prices' => $roomCalendar['calendar_prices'],
                    ];
                })
                ->values();

            $selectedRoomTypeId = (int) $request->get('room_type_id', 0);
            $calendarProduct = $this->resolveCalendarProduct($product, $selectedRoomTypeId ?: null);
            $calendarBasePrice = (float) (($calendarProduct->sale_price ?: $calendarProduct->price) ?? 0);
            $calendarData = $this->getCalendarData((int) $calendarProduct->id, $calendarBasePrice);

            $displayPrice = (float) (($raw->sale_price ?: $raw->price) ?? 0);

            $relatedProducts = DB::table('ec_product_category_product as pcp')
                ->join('ec_product_category_product as rel_pcp', 'rel_pcp.category_id', '=', 'pcp.category_id')
                ->join('ec_products as p', 'p.id', '=', 'rel_pcp.product_id')
                ->leftJoin('slugs as s', function ($join) {
                    $join->on('s.reference_id', '=', 'p.id')
                        ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
                })
                ->where('pcp.product_id', $product->id)
                ->where('rel_pcp.product_id', '!=', $product->id)
                ->where('p.is_variation', 0)
                ->where('p.status', 'published')
                ->select([
                    'p.id',
                    'p.name',
                    'p.sku',
                    'p.price',
                    'p.sale_price',
                    'p.image',
                    'p.thumbnail',
                    'p.images',
                    'p.description',
                    'p.content',
                    'p.address',
                    'p.type',
                    'p.level_star',
                    'p.status',
                    'p.created_at',
                    's.key as slug',
                ])
                ->distinct()
                ->orderByDesc('p.id')
                ->limit(8)
                ->get()
                ->map(fn($item) => $this->transformListItem($item))
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'message' => 'Product detail fetched successfully.',
                'data' => [
                    'id' => (int) $raw->id,
                    'name' => $raw->name,
                    'slug' => $raw->slug,
                    'sku' => $raw->sku,
                    'product_type' => $raw->type ?? 'product',
                    'price' => (float) ($raw->price ?? 0),
                    'sale_price' => (float) ($raw->sale_price ?? 0),
                    'display_price' => $displayPrice,
                    'price_label' => $this->formatMoney($displayPrice),

                    'thumbnail' => $thumbnail,
                    'image' => $this->formatImageUrl($raw->image ?? null) ?? $thumbnail,
                    'gallery' => $gallery,
                    'hero_images' => $heroImages,
                    'preview_images' => $heroImages,

                    'description' => $raw->description ?? '',
                    'content' => $raw->content ?? '',
                    'location' => $raw->address ?? '',
                    'rating' => (int) ($raw->level_star ?? 0),
                    'views' => (int) ($raw->views ?? 0),
                    'is_featured' => (bool) ($raw->is_featured ?? false),
                    'status' => $raw->status,
                    'created_at' => $raw->created_at,

                    'amenities' => collect($extensions)->pluck('name')->filter()->values()->all(),
                    'extensions' => $extensions,

                    'room_types' => $groupedRooms->map(fn($room) => [
                        'id' => $room['id'],
                        'name' => $room['name'],
                        'description' => $room['description'],
                        'price' => $room['price'],
                        'price_value' => $room['price_value'],
                        'image' => $room['image'],
                        'max_adults' => $room['max_adults'],
                        'max_children' => $room['max_children'],
                    ])->values()->all(),

                    'grouped_rooms' => $groupedRooms->values()->all(),

                    'extra_services' => collect($services)->map(fn($service) => [
                        'id' => $service['id'],
                        'name' => $service['name'],
                        'price' => $service['price_label'],
                        'price_value' => $service['price'],
                        'image' => $service['image'],
                    ])->values()->all(),

                    'services' => $services,

                    'policies' => $this->decodeMetaLines($meta['policy'] ?? null),
                    'notes' => $this->decodeMetaLines($meta['rule'] ?? null),
                    'faqs' => [],

                    'meta' => [
                        'policy' => $this->decodeMetaLines($meta['policy'] ?? null),
                        'rule' => $this->decodeMetaLines($meta['rule'] ?? null),
                        'beds' => (int) $this->decodeMetaScalar($meta['beds'] ?? 0, 0),
                        'max_adults' => (int) $this->decodeMetaScalar($meta['max_adults'] ?? 0, 0),
                        'max_children' => (int) $this->decodeMetaScalar($meta['max_children'] ?? 0, 0),
                        'children_surplus_fee' => (float) $this->decodeMetaScalar($meta['children_surplus_fee'] ?? 0, 0),
                        'addon_fee' => (float) $this->decodeMetaScalar($meta['addonFee'] ?? 0, 0),
                        'service_fee' => (float) $this->decodeMetaScalar($meta['serviceFee'] ?? 0, 0),
                        'size' => $this->decodeMetaScalar($meta['size'] ?? null),
                    ],

                    'categories' => $categories,

                    'calendar_room_type_id' => (int) $calendarProduct->id,
                    'calendar_room_type_name' => $calendarProduct->name ?? '',
                    'available_dates' => $calendarData['available_dates'],
                    'booked_dates' => $calendarData['booked_dates'],
                    'calendar_prices' => $calendarData['calendar_prices'],

                    'related_products' => $relatedProducts,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product detail error',
                'error' => $e->getMessage(),
                'slug' => $slug,
            ], 500);
        }
    }

    public function calendar(Request $request, string $slug): JsonResponse
    {
        try {
            $product = $this->findProductBySlug($slug);

            if (! $product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            $selectedRoomTypeId = (int) $request->get('room_type_id', 0);
            $calendarProduct = $this->resolveCalendarProduct($product, $selectedRoomTypeId ?: null);

            $basePrice = (float) (($calendarProduct->sale_price ?: $calendarProduct->price) ?? 0);
            $calendarData = $this->getCalendarData((int) $calendarProduct->id, $basePrice);

            return response()->json([
                'success' => true,
                'message' => 'Calendar fetched successfully.',
                'data' => [
                    'product_id' => (int) $product->id,
                    'slug' => $slug,
                    'calendar_room_type_id' => (int) $calendarProduct->id,
                    'calendar_room_type_name' => $calendarProduct->name ?? '',
                    'booked_dates' => $calendarData['booked_dates'],
                    'available_dates' => $calendarData['available_dates'],
                    'calendar_prices' => $calendarData['calendar_prices'],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar fetch error.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bookingPreview(Request $request, string $slug): JsonResponse
    {
        try {
            $product = $this->findProductBySlug($slug);

            if (! $product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            $validated = $request->validate([
                'check_in' => ['required', 'date'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'adult_count' => ['nullable', 'integer', 'min:0'],
                'children_count' => ['nullable', 'integer', 'min:0'],
                'room_type_id' => ['nullable', 'integer'],
                'service_ids' => ['nullable', 'array'],
                'service_ids.*' => ['integer'],
            ]);

            $checkIn = Carbon::parse($validated['check_in'])->startOfDay();
            $checkOut = Carbon::parse($validated['check_out'])->startOfDay();

            $adultCount = (int) ($validated['adult_count'] ?? 0);
            $childrenCount = (int) ($validated['children_count'] ?? 0);
            $roomTypeId = isset($validated['room_type_id']) ? (int) $validated['room_type_id'] : null;
            $serviceIds = collect($validated['service_ids'] ?? [])
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $nights = $checkIn->diffInDays($checkOut);

            if ($nights <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid booking dates.',
                ], 422);
            }

            $selectedRoom = $this->resolveCalendarProduct($product, $roomTypeId);
            $pricingMeta = $this->getProductMeta((int) $selectedRoom->id);

            $nightlyPrice = (float) (($selectedRoom->sale_price ?: $selectedRoom->price) ?? 0);
            $calendarData = $this->getCalendarData((int) $selectedRoom->id, $nightlyPrice);
            $bookedDates = $calendarData['booked_dates'];

            $calendarPriceMap = collect($calendarData['calendar_prices'])
                ->mapWithKeys(function ($item) {
                    $date = $this->normalizeDateString($item['date'] ?? null);

                    if (! $date) {
                        return [];
                    }

                    return [
                        $date => [
                            'date' => $date,
                            'price' => (float) ($item['price'] ?? 0),
                            'price_label' => $item['price_label'] ?? '',
                            'is_available' => (bool) ($item['is_available'] ?? false),
                        ],
                    ];
                })
                ->all();

            $stayDates = [];
            $stayDatePrices = [];
            $cursor = $checkIn->copy();

            while ($cursor->lt($checkOut)) {
                $date = $cursor->format('Y-m-d');
                $stayDates[] = $date;

                $calendarItem = $calendarPriceMap[$date] ?? null;

                if (! $calendarItem || ! ($calendarItem['is_available'] ?? false)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected date range contains unavailable dates.',
                        'data' => [
                            'conflict_date' => $date,
                            'booked_dates' => $bookedDates,
                            'calendar_room_type_id' => (int) $selectedRoom->id,
                            'calendar_room_type_name' => $selectedRoom->name ?? '',
                        ],
                    ], 422);
                }

                $stayDatePrices[] = [
                    'date' => $date,
                    'price' => (float) ($calendarItem['price'] ?? $nightlyPrice),
                    'price_label' => $calendarItem['price_label'] ?? $this->formatMoney($nightlyPrice),
                ];

                $cursor->addDay();
            }

            $roomSubtotal = collect($stayDatePrices)->sum('price');

            $roomMaxAdults = (int) $this->decodeMetaScalar($pricingMeta['max_adults'] ?? 0, 0);
            $roomMaxChildren = (int) $this->decodeMetaScalar($pricingMeta['max_children'] ?? 0, 0);
            $childrenSurplusFee = (float) $this->decodeMetaScalar($pricingMeta['children_surplus_fee'] ?? 0, 0);
            $addonFee = (float) $this->decodeMetaScalar($pricingMeta['addonFee'] ?? 0, 0);
            $serviceFee = (float) $this->decodeMetaScalar($pricingMeta['serviceFee'] ?? 0, 0);

            $extraAdultCount = max(0, $adultCount - $roomMaxAdults);
            $extraChildrenCount = max(0, $childrenCount - $roomMaxChildren);

            $roomSubtotal = $nightlyPrice * $nights;
            $childrenSurchargeTotal = $childrenSurplusFee * $extraChildrenCount * $nights;
            $addonTotal = $addonFee * $extraAdultCount * $nights;

            $selectedServices = collect($product->services ?? [])
                ->filter(fn($service) => $serviceIds->contains((int) $service->id))
                ->values();

            $servicesSubtotal = $selectedServices->sum(function ($service) {
                return (float) ($service->price ?? 0);
            });

            $grandTotal = $roomSubtotal + $childrenSurchargeTotal + $addonTotal + $serviceFee + $servicesSubtotal;

            return response()->json([
                'success' => true,
                'message' => 'Booking preview fetched successfully.',
                'data' => [
                    'product_id' => (int) $product->id,
                    'product_name' => $product->name,
                    'slug' => $slug,

                    'check_in' => $checkIn->format('Y-m-d'),
                    'check_out' => $checkOut->format('Y-m-d'),
                    'nights' => $nights,
                    'stay_dates' => $stayDates,

                    'room_type' => [
                        'id' => (int) $selectedRoom->id,
                        'name' => $selectedRoom->name ?? '',
                        'nightly_price' => (float) $nightlyPrice,
                        'nightly_price_label' => $this->formatMoney($nightlyPrice),
                        'max_adults' => $roomMaxAdults,
                        'max_children' => $roomMaxChildren,
                    ],

                    'calendar_room_type_id' => (int) $selectedRoom->id,
                    'calendar_room_type_name' => $selectedRoom->name ?? '',

                    'guest_counts' => [
                        'adult_count' => $adultCount,
                        'children_count' => $childrenCount,
                        'extra_adult_count' => $extraAdultCount,
                        'extra_children_count' => $extraChildrenCount,
                    ],

                    'selected_services' => $selectedServices->map(function ($service) {
                        $price = (float) ($service->price ?? 0);

                        return [
                            'id' => (int) $service->id,
                            'name' => $service->name ?? '',
                            'price' => $price,
                            'price_label' => $this->formatMoney($price),
                        ];
                    })->values()->all(),

                    'pricing' => [
                        'nightly_price' => (float) $nightlyPrice,
                        'nightly_price_label' => $this->formatMoney($nightlyPrice),

                        'room_subtotal' => (float) $roomSubtotal,
                        'room_subtotal_label' => $this->formatMoney($roomSubtotal),

                        'children_surplus_fee' => (float) $childrenSurplusFee,
                        'children_surplus_fee_label' => $this->formatMoney($childrenSurplusFee),
                        'children_surplus_total' => (float) $childrenSurchargeTotal,
                        'children_surplus_total_label' => $this->formatMoney($childrenSurchargeTotal),

                        'addon_fee' => (float) $addonFee,
                        'addon_fee_label' => $this->formatMoney($addonFee),
                        'addon_total' => (float) $addonTotal,
                        'addon_total_label' => $this->formatMoney($addonTotal),

                        'service_fee' => (float) $serviceFee,
                        'service_fee_label' => $this->formatMoney($serviceFee),

                        'services_subtotal' => (float) $servicesSubtotal,
                        'services_subtotal_label' => $this->formatMoney($servicesSubtotal),

                        'grand_total' => (float) $grandTotal,
                        'grand_total_label' => $this->formatMoney($grandTotal),
                    ],

                    'booked_dates' => $bookedDates,
                    'stay_date_prices' => $stayDatePrices,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking preview error.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function findProductBySlug(string $slug): ?Product
    {
        return Product::query()
            ->where('is_variation', 0)
            ->where('status', 'published')
            ->whereExists(function ($query) use ($slug) {
                $query->select(DB::raw(1))
                    ->from('slugs')
                    ->whereColumn('slugs.reference_id', 'ec_products.id')
                    ->where('slugs.reference_type', 'Botble\\Ecommerce\\Models\\Product')
                    ->where('slugs.key', $slug);
            })
            ->with([
                'groupedProduct',
                'groupedProduct.slugable',
                'services',
                'extensions',
            ])
            ->first();
    }

    private function getGroupedRoomsForCalendar(int $parentProductId)
    {
        return Product::query()
            ->select('ec_products.*')
            ->join('ec_grouped_products', 'ec_grouped_products.product_id', '=', 'ec_products.id')
            ->where('ec_grouped_products.parent_product_id', $parentProductId)
            ->where('ec_products.status', 'published')
            ->where('ec_products.is_variation', 0)
            ->orderByRaw('COALESCE(ec_products.sale_price, ec_products.price) asc')
            ->orderBy('ec_products.id')
            ->with('slugable')
            ->get();
    }

    private function resolveCalendarProduct(Product $product, ?int $roomTypeId = null): Product
    {
        $groupedRooms = $this->getGroupedRoomsForCalendar((int) $product->id);

        if ($roomTypeId) {
            $matched = $groupedRooms->first(fn($room) => (int) $room->id === (int) $roomTypeId);

            if ($matched) {
                return $matched;
            }
        }

        if ($groupedRooms->isNotEmpty()) {
            return $groupedRooms->first();
        }

        return $product;
    }

    private function getCalendarData(int $productId, float $fallbackPrice = 0): array
    {
        $variationCalendar = $this->getCalendarDataFromVariations($productId, $fallbackPrice);
        $orderCalendar = $this->getCalendarDataFromOrders($productId, $fallbackPrice);

        $calendarMap = [];

        // Variation calendar là nguồn sự thật chính
        foreach ($variationCalendar['calendar_prices'] as $item) {
            $date = $this->normalizeDateString($item['date'] ?? null);

            if (! $date) {
                continue;
            }

            $calendarMap[$date] = [
                'date' => $date,
                'price' => (int) ($item['price'] ?? round($fallbackPrice)),
                'price_label' => $item['price_label'] ?? $this->formatMoney($fallbackPrice),
                'is_available' => (bool) ($item['is_available'] ?? false),
            ];
        }

        // Order chỉ là fallback cho những ngày chưa có variation record
        foreach ($orderCalendar['booked_dates'] as $date) {
            $normalizedDate = $this->normalizeDateString($date);

            if (! $normalizedDate) {
                continue;
            }

            if (! isset($calendarMap[$normalizedDate])) {
                $calendarMap[$normalizedDate] = [
                    'date' => $normalizedDate,
                    'price' => (int) round($fallbackPrice),
                    'price_label' => $this->formatMoney($fallbackPrice),
                    'is_available' => false,
                ];
            }
        }

        ksort($calendarMap);

        $calendarPrices = array_values($calendarMap);

        $availableDates = collect($calendarPrices)
            ->filter(fn($item) => ($item['is_available'] ?? false) === true)
            ->pluck('date')
            ->values()
            ->all();

        $bookedDates = collect($calendarPrices)
            ->filter(fn($item) => ($item['is_available'] ?? false) === false)
            ->pluck('date')
            ->values()
            ->all();

        return [
            'available_dates' => $availableDates,
            'booked_dates' => $bookedDates,
            'calendar_prices' => $calendarPrices,
        ];
    }

    private function getCalendarDataFromVariations(int $productId, float $fallbackPrice = 0): array
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addMonths(12)->endOfDay();

        $rows = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->where('ec_product_variations.configurable_product_id', $productId)
            ->where('ec_product_attribute_sets.slug', self::HOTEL_VILLA_DATE_ATTRIBUTE_SET_SLUG)
            ->whereDate('ec_product_attributes.title', '>=', $startDate->format('Y-m-d'))
            ->whereDate('ec_product_attributes.title', '<=', $endDate->format('Y-m-d'))
            ->select([
                'ec_product_attributes.title as date',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.with_storehouse_management',
                'ec_products.stock_status',
                'ec_products.quantity',
            ])
            ->get();

        if ($rows->isEmpty()) {
            return [
                'available_dates' => [],
                'booked_dates' => [],
                'calendar_prices' => [],
            ];
        }

        $dateMap = [];

        foreach ($rows as $item) {
            $date = $this->normalizeDateString($item->date);

            if (! $date) {
                continue;
            }

            $isAvailable = false;

            if ((int) $item->with_storehouse_management === 1) {
                $isAvailable = (int) $item->quantity > 0;
            } else {
                $isAvailable = (string) $item->stock_status === 'in_stock';
            }

            $price = (float) (($item->sale_price ?: $item->price) ?? $fallbackPrice);

            if (! isset($dateMap[$date])) {
                $dateMap[$date] = [
                    'date' => $date,
                    'price' => (int) round($price),
                    'price_label' => $this->formatMoney($price),
                    'is_available' => $isAvailable,
                ];
                continue;
            }

            $current = $dateMap[$date];

            if ($isAvailable && ! $current['is_available']) {
                $dateMap[$date] = [
                    'date' => $date,
                    'price' => (int) round($price),
                    'price_label' => $this->formatMoney($price),
                    'is_available' => true,
                ];
                continue;
            }

            if ($isAvailable === $current['is_available'] && (int) round($price) < (int) $current['price']) {
                $dateMap[$date] = [
                    'date' => $date,
                    'price' => (int) round($price),
                    'price_label' => $this->formatMoney($price),
                    'is_available' => $isAvailable,
                ];
            }
        }

        ksort($dateMap);

        $calendarPrices = array_values($dateMap);

        $availableDates = collect($calendarPrices)
            ->filter(fn($item) => ($item['is_available'] ?? false) === true)
            ->pluck('date')
            ->values()
            ->all();

        $bookedDates = collect($calendarPrices)
            ->filter(fn($item) => ($item['is_available'] ?? false) === false)
            ->pluck('date')
            ->values()
            ->all();

        return [
            'available_dates' => $availableDates,
            'booked_dates' => $bookedDates,
            'calendar_prices' => $calendarPrices,
        ];
    }

    private function getCalendarDataFromOrders(int $productId, float $fallbackPrice = 0): array
    {
        $rows = DB::table('ec_order_product as op')
            ->join('ec_orders as o', 'o.id', '=', 'op.order_id')
            ->where('op.product_id', $productId)
            ->whereNotIn('o.status', [
                'canceled',
                'cancelled',
                'returned',
                'refunded',
                'failed',
            ])
            ->select([
                'op.options',
                'op.product_id',
                'o.status',
            ])
            ->get();

        $bookedDates = $rows
            ->flatMap(function ($row) {
                return $this->extractBookedDatesFromOptions($row->options);
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $calendarPrices = collect($bookedDates)
            ->map(function ($date) use ($fallbackPrice) {
                return [
                    'date' => $date,
                    'price' => (int) round($fallbackPrice),
                    'price_label' => $this->formatMoney($fallbackPrice),
                    'is_available' => false,
                ];
            })
            ->values()
            ->all();

        return [
            'available_dates' => [],
            'booked_dates' => $bookedDates,
            'calendar_prices' => $calendarPrices,
        ];
    }

    private function extractBookedDatesFromOptions($options): array
    {
        if (empty($options)) {
            return [];
        }

        if (is_string($options)) {
            $decoded = json_decode($options, true);
            $options = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        if (! is_array($options)) {
            return [];
        }

        $days = $options['days']
            ?? $options['booking_days']
            ?? $options['booked_dates']
            ?? null;

        if (is_string($days)) {
            $decodedDays = json_decode($days, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $days = $decodedDays;
            } else {
                $days = array_map('trim', explode(',', $days));
            }
        }

        if (is_array($days) && ! empty($days)) {
            return collect($days)
                ->map(function ($day) {
                    if (is_array($day)) {
                        $day = $day['date'] ?? $day['day'] ?? null;
                    }

                    return $this->normalizeDateString($day);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $checkIn = $options['check_in'] ?? $options['checkin'] ?? null;
        $checkOut = $options['check_out'] ?? $options['checkout'] ?? null;

        if (! $checkIn || ! $checkOut) {
            return [];
        }

        try {
            $start = Carbon::parse($checkIn)->startOfDay();
            $end = Carbon::parse($checkOut)->startOfDay();

            if (! $end->gt($start)) {
                return [];
            }

            $dates = [];
            $cursor = $start->copy();

            while ($cursor->lt($end)) {
                $dates[] = $cursor->format('Y-m-d');
                $cursor->addDay();
            }

            return $dates;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function normalizeDateString($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function transformListItem(object $item): array
    {
        $gallery = $this->parseGallery($item->images ?? null);
        $thumbnail = $this->resolveThumbnail(
            $item->thumbnail ?? null,
            $item->image ?? null,
            $gallery,
        );

        return [
            'id' => (int) $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'sku' => $item->sku,
            'product_type' => $item->type ?? 'product',
            'price' => (float) ($item->price ?? 0),
            'sale_price' => (float) ($item->sale_price ?? 0),
            'thumbnail' => $thumbnail,
            'image' => $this->formatImageUrl($item->image ?? null) ?? $thumbnail,
            'gallery' => $gallery,
            'location' => $item->address ?? '',
            'rating' => (int) ($item->level_star ?? 0),
            'description' => Str::limit(strip_tags((string) ($item->description ?? '')), 120),
            'status' => $item->status,
            'created_at' => $item->created_at,
        ];
    }

    private function getProductMeta(int $productId): array
    {
        try {
            return DB::table('meta_boxes')
                ->where('reference_id', $productId)
                ->where('reference_type', 'Botble\\Ecommerce\\Models\\Product')
                ->pluck('meta_value', 'meta_key')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function parseGallery($images): array
    {
        if (empty($images)) {
            return [];
        }

        if (is_array($images)) {
            return collect($images)
                ->filter()
                ->map(fn($item) => $this->formatImageUrl(is_array($item) ? ($item['image'] ?? $item['url'] ?? null) : $item))
                ->filter()
                ->values()
                ->all();
        }

        if (is_string($images)) {
            $decoded = json_decode($images, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return collect($decoded)
                    ->filter()
                    ->map(fn($item) => $this->formatImageUrl(is_array($item) ? ($item['image'] ?? $item['url'] ?? null) : $item))
                    ->filter()
                    ->values()
                    ->all();
            }

            $single = $this->formatImageUrl($images);

            return $single ? [$single] : [];
        }

        return [];
    }

    private function resolveThumbnail($thumbnail, $image, array $gallery): ?string
    {
        if (is_array($thumbnail)) {
            $thumbnail = $thumbnail['image'] ?? $thumbnail['url'] ?? null;
        }

        if (is_array($image)) {
            $image = $image['image'] ?? $image['url'] ?? null;
        }

        if ($thumbnail) {
            return $this->formatImageUrl($thumbnail);
        }

        if ($image) {
            return $this->formatImageUrl($image);
        }

        return $gallery[0] ?? null;
    }

    private function formatImageUrl($image): ?string
    {
        if (! $image) {
            return null;
        }

        if (is_array($image)) {
            $image = $image['image'] ?? $image['url'] ?? null;
        }

        if (! $image) {
            return null;
        }

        $image = trim((string) $image);

        if ($image === '') {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $base = rtrim(
            config('app.media_base_url', env('MEDIA_BASE_URL', 'https://storage.mistudio.asia/abogo_travel-9211576546')),
            '/'
        );

        return $base . '/' . ltrim($image, '/');
    }

    private function formatMoney(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }

    private function decodeMetaScalar($value, $default = null)
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_array($value)) {
            return $value[0] ?? $default;
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                return $decoded[0] ?? $default;
            }

            return $decoded ?? $default;
        }

        return $value;
    }

    private function decodeMetaLines($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                $text = implode("\n", array_filter($decoded));
            } else {
                $text = (string) $decoded;
            }
        } else {
            $text = (string) $value;
        }

        return collect(preg_split('/\r\n|\r|\n/', strip_tags($text)))
            ->map(fn($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function cleanHtmlText(?string $html): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags((string) $html)));
    }
}
