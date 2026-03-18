<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Botble\Ecommerce\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
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
            ->values();

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

    public function show(string $slug): JsonResponse
    {
        try {
            $product = Product::query()
                ->where('is_variation', 0)
                ->where('status', 'published')
                ->whereHas('slugable', function ($query) use ($slug) {
                    $query->where('key', $slug);
                })
                ->with([
                    'groupedProduct',
                    'groupedProduct.slugable',
                    'services',
                    'extensions',
                ])
                ->first();

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

            $extensions = collect($product->extensions ?? [])->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'name' => $item->name ?? '',
                    'image' => $this->formatImageUrl($item->image ?? null),
                ];
            })->values()->all();

            $services = collect($product->services ?? [])->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'name' => $item->name ?? '',
                    'price' => (float) ($item->price ?? 0),
                    'price_label' => $this->formatMoney((float) ($item->price ?? 0)),
                    'image' => $this->formatImageUrl($item->image ?? null),
                ];
            })->values()->all();

            $groupedRooms = collect($product->groupedProduct ?? [])
                ->sortBy(function ($room) {
                    return (float) (($room->sale_price ?: $room->price) ?? 0);
                })
                ->values()
                ->map(function ($room) {
                    $roomMeta = $this->getProductMeta((int) $room->id);
                    $roomGallery = $this->parseGallery($room->images ?? null);
                    $roomThumb = $this->resolveThumbnail(
                        $room->thumbnail ?? null,
                        $room->image ?? null,
                        $roomGallery,
                    );

                    $currentDatePrice = $this->getCurrentDatePriceSafe($room);

                    return [
                        'id' => (int) $room->id,
                        'name' => $room->name ?? '',
                        'slug' => optional($room->slugable)->key,
                        'description' => $this->cleanHtmlText($room->description ?? ''),
                        'image' => $roomThumb,
                        'price' => (float) $currentDatePrice,
                        'price_label' => $currentDatePrice > 0
                            ? $this->formatMoney((float) $currentDatePrice)
                            : 'Hết Phòng',
                        'max_adults' => (int) $this->decodeMetaScalar($roomMeta['max_adults'] ?? 0, 0),
                        'max_children' => (int) $this->decodeMetaScalar($roomMeta['max_children'] ?? 0, 0),
                        'beds' => (int) $this->decodeMetaScalar($roomMeta['beds'] ?? 0, 0),
                        'size' => $this->decodeMetaScalar($roomMeta['size'] ?? null),
                        'children_surplus_fee' => (float) $this->decodeMetaScalar($roomMeta['children_surplus_fee'] ?? 0, 0),
                        'addon_fee' => (float) $this->decodeMetaScalar($roomMeta['addonFee'] ?? 0, 0),
                    ];
                })
                ->all();

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

            $displayPrice = (float) (($raw->sale_price ?: $raw->price) ?? 0);

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

                    'room_types' => collect($groupedRooms)->map(fn($room) => [
                        'id' => $room['id'],
                        'name' => $room['name'],
                        'description' => $room['description'],
                        'price' => $room['price_label'],
                        'image' => $room['image'],
                        'max_adults' => $room['max_adults'],
                        'max_children' => $room['max_children'],
                    ])->values()->all(),
                    'grouped_rooms' => $groupedRooms,

                    'extra_services' => collect($services)->map(fn($service) => [
                        'id' => $service['id'],
                        'name' => $service['name'],
                        'price' => $service['price_label'],
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
                    'booked_dates' => [],
                    'calendar_prices' => [],
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

            return [$this->formatImageUrl($images)];
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

    private function getCurrentDatePriceSafe($product): float
    {
        try {
            if (function_exists('get_current_date_price')) {
                $price = get_current_date_price($product);

                if ($price) {
                    return (float) $price;
                }
            }
        } catch (\Throwable $e) {
        }

        return (float) (($product->sale_price ?: $product->price) ?? 0);
    }
}
