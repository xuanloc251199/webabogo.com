<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
        $product = DB::table('ec_products as p')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'p.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
            })
            ->where('p.is_variation', 0)
            ->where('p.status', 'published')
            ->where('s.key', $slug)
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

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $gallery = $this->parseGallery($product->images ?? null);
        $meta = $this->getProductMeta((int) $product->id);

        $categoryRows = DB::table('ec_product_category_product as pcp')
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
            ->values();

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
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Product detail fetched successfully.',
            'data' => [
                'id' => (int) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'product_type' => $product->type ?? 'product',
                'price' => (float) ($product->price ?? 0),
                'sale_price' => (float) ($product->sale_price ?? 0),
                'display_price' => (float) (($product->sale_price ?: $product->price) ?? 0),
                'thumbnail' => $this->resolveThumbnail(
                    $product->thumbnail ?? null,
                    $product->image ?? null,
                    $gallery,
                ),
                'image' => $this->formatImageUrl($product->image ?? null)
                    ?? $this->resolveThumbnail($product->thumbnail ?? null, $product->image ?? null, $gallery),
                'gallery' => $gallery,
                'description' => $product->description,
                'content' => $product->content,
                'location' => $product->address ?? '',
                'rating' => (int) ($product->level_star ?? 0),
                'views' => (int) ($product->views ?? 0),
                'is_featured' => (bool) ($product->is_featured ?? false),
                'status' => $product->status,
                'created_at' => $product->created_at,
                'meta' => [
                    'policy' => $meta['policy'] ?? null,
                    'rule' => $meta['rule'] ?? null,
                    'beds' => isset($meta['beds']) ? (int) $meta['beds'] : 0,
                    'max_adults' => isset($meta['max_adults']) ? (int) $meta['max_adults'] : 0,
                    'max_children' => isset($meta['max_children']) ? (int) $meta['max_children'] : 0,
                    'children_surplus_fee' => isset($meta['children_surplus_fee']) ? (float) $meta['children_surplus_fee'] : 0,
                    'addon_fee' => isset($meta['addonFee']) ? (float) $meta['addonFee'] : 0,
                    'service_fee' => isset($meta['serviceFee']) ? (float) $meta['serviceFee'] : 0,
                    'size' => $meta['size'] ?? null,
                ],
                'categories' => $categoryRows,
                'related_products' => $relatedProducts,
            ],
        ]);
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
            ->map(fn($item) => $this->formatImageUrl($item))
            ->values()
            ->all();
    }

    private function resolveThumbnail(?string $thumbnail, ?string $image, array $gallery): ?string
    {
        if ($thumbnail) {
            return $this->formatImageUrl($thumbnail);
        }

        if ($image) {
            return $this->formatImageUrl($image);
        }

        return $gallery[0] ?? null;
    }

    private function formatImageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        $image = trim($image);

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $base = rtrim(
            config('app.media_base_url', env('MEDIA_BASE_URL', 'https://storage.mistudio.asia/abogo_travel-9211576546')),
            '/'
        );

        return $base . '/' . ltrim($image, '/');
    }
}
