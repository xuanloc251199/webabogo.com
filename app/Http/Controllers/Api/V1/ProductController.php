<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $type = trim((string) $request->get('type', ''));
        $paginate = (int) $request->get('paginate', 0);
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        if ($perPage <= 0) {
            $perPage = 10;
        }

        if ($perPage > 100) {
            $perPage = 100;
        }

        $query = DB::table('ec_products as p')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'p.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
            })
            ->where('p.is_variation', 0)
            ->select([
                'p.id',
                'p.name',
                'p.sku',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.images',
                'p.description',
                'p.content',
                'p.status',
                'p.created_at',
                's.key as slug',
            ])
            ->orderByDesc('p.id');

        if ($keyword !== '') {
            $query->where('p.name', 'like', '%' . $keyword . '%');
        }

        if ($type !== '') {
            $query->where(function ($subQuery) use ($type) {
                $normalizedType = mb_strtolower($type);

                if ($normalizedType === 'tour') {
                    $subQuery->where('p.name', 'like', '%tour%')
                        ->orWhere('s.key', 'like', '%tour%');
                } elseif ($normalizedType === 'villa') {
                    $subQuery->where('p.name', 'like', '%villa%')
                        ->orWhere('s.key', 'like', '%villa%');
                } elseif ($normalizedType === 'hotel') {
                    $subQuery->where('p.name', 'like', '%hotel%')
                        ->orWhere('p.name', 'like', '%khách sạn%')
                        ->orWhere('p.name', 'like', '%khach san%')
                        ->orWhere('s.key', 'like', '%hotel%')
                        ->orWhere('s.key', 'like', '%khach-san%');
                }
            });
        }

        if ($paginate === 1) {
            $products = $query->paginate($perPage, ['*'], 'page', $page);

            $items = collect($products->items())
                ->map(function ($item) {
                    return $this->transformListItem($item);
                })
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully.',
                'data' => $items,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ],
            ]);
        }

        $rows = $query->get();

        $items = collect($rows)
            ->map(function ($item) {
                return $this->transformListItem($item);
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully.',
            'total' => $items->count(),
            'data' => $items,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = DB::table('ec_products as p')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'p.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
            })
            ->where('p.is_variation', 0)
            ->where('s.key', $slug)
            ->select([
                'p.id',
                'p.name',
                'p.sku',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.images',
                'p.description',
                'p.content',
                'p.status',
                'p.created_at',
                'p.updated_at',
                's.key as slug',
            ])
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
                'data' => null,
            ], 404);
        }

        $meta = $this->getProductMeta((int) $product->id);

        return response()->json([
            'success' => true,
            'message' => 'Product fetched successfully.',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'product_type' => $this->guessProductType($product->name, $product->slug),
                'price' => (float) ($product->price ?? 0),
                'sale_price' => (float) ($product->sale_price ?? 0),
                'image' => $this->formatImageUrl($product->image),
                'gallery' => $this->parseGallery($product->images),
                'description' => $product->description,
                'content' => $product->content,
                'status' => $product->status,
                'meta' => [
                    'policy' => $meta['policy'] ?? null,
                    'rule' => $meta['rule'] ?? null,
                    'beds' => $meta['beds'] ?? null,
                    'max_adults' => isset($meta['max_adults']) ? (int) $meta['max_adults'] : 0,
                    'max_children' => isset($meta['max_children']) ? (int) $meta['max_children'] : 0,
                    'children_surplus_fee' => isset($meta['children_surplus_fee']) ? (float) $meta['children_surplus_fee'] : 0,
                    'addon_fee' => isset($meta['addonFee']) ? (float) $meta['addonFee'] : 0,
                    'service_fee' => isset($meta['serviceFee']) ? (float) $meta['serviceFee'] : 0,
                    'size' => $meta['size'] ?? null,
                ],
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ],
        ]);
    }

    private function transformListItem(object $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'sku' => $item->sku,
            'product_type' => $this->guessProductType($item->name, $item->slug),
            'price' => (float) ($item->price ?? 0),
            'sale_price' => (float) ($item->sale_price ?? 0),
            'image' => $this->formatImageUrl($item->image),
            'description' => $item->description,
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

    private function parseGallery(?string $images): array
    {
        if (! $images) {
            return [];
        }

        $decoded = json_decode($images, true);

        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->filter()
            ->map(fn ($item) => $this->formatImageUrl($item))
            ->values()
            ->all();
    }

    private function guessProductType(?string $name, ?string $slug): string
    {
        $text = mb_strtolower(trim(($name ?? '') . ' ' . ($slug ?? '')));

        if (str_contains($text, 'tour')) {
            return 'tour';
        }

        if (str_contains($text, 'villa')) {
            return 'villa';
        }

        if (
            str_contains($text, 'hotel') ||
            str_contains($text, 'khach-san') ||
            str_contains($text, 'khách sạn') ||
            str_contains($text, 'khach san')
        ) {
            return 'hotel';
        }

        return 'product';
    }

    private function formatImageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        return url('storage/' . ltrim($image, '/'));
    }
}