<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $stayCategories = $this->getStayCategories();
        $serviceCategories = $this->getServiceCategories();
        $stays = $this->getStayProducts();
        $normalProducts = $this->getNormalProducts();
        $news = $this->getFeaturedNews();

        $featuredStays = $stays
            ->sortByDesc(fn($item) => sprintf(
                '%d-%010d-%010d',
                $item['is_featured'] ? 1 : 0,
                $item['views'],
                $item['id']
            ))
            ->take(6)
            ->values();

        $sections = [
            $this->buildSection(
                key: 'guest_favorites',
                title: 'Khách yêu thích',
                subtitle: 'Các nơi lưu trú nổi bật trên Abogo',
                items: $stays
                    ->filter(fn($item) => $item['is_featured'] || $item['views'] > 0)
                    ->sortByDesc(fn($item) => sprintf(
                        '%d-%010d-%010d',
                        $item['is_featured'] ? 1 : 0,
                        $item['views'],
                        $item['id']
                    ))
                    ->take(6)
            ),
            $this->buildSection(
                key: 'beachfront_villas',
                title: 'Villa gần biển',
                subtitle: 'Phù hợp nghỉ dưỡng, nhóm bạn, gia đình',
                items: $stays
                    ->filter(fn($item) => $item['type'] === 'villa' && ($item['tags']['beachfront'] ?? false))
                    ->sortByDesc(fn($item) => sprintf(
                        '%d-%010d-%010d',
                        $item['is_featured'] ? 1 : 0,
                        $item['views'],
                        $item['id']
                    ))
                    ->take(6)
            ),
            $this->buildSection(
                key: 'private_pool_villas',
                title: 'Villa hồ bơi riêng',
                subtitle: 'Bộ sưu tập được xem nhiều',
                items: $stays
                    ->filter(fn($item) => $item['type'] === 'villa' && ($item['tags']['private_pool'] ?? false))
                    ->sortByDesc(fn($item) => sprintf(
                        '%d-%010d-%010d',
                        $item['is_featured'] ? 1 : 0,
                        $item['views'],
                        $item['id']
                    ))
                    ->take(6)
            ),
            $this->buildSection(
                key: 'family_villas',
                title: 'Villa cho gia đình',
                subtitle: 'Ưu tiên không gian rộng, nhiều người ở',
                items: $stays
                    ->filter(fn($item) => $item['type'] === 'villa' && ($item['tags']['family_friendly'] ?? false))
                    ->sortByDesc(fn($item) => sprintf(
                        '%d-%010d-%010d',
                        $item['is_featured'] ? 1 : 0,
                        $item['views'],
                        $item['id']
                    ))
                    ->take(6)
            ),
            $this->buildSection(
                key: 'group_villas',
                title: 'Villa cho nhóm đông',
                subtitle: 'Phù hợp team building và nhóm bạn',
                items: $stays
                    ->filter(fn($item) => $item['type'] === 'villa' && ($item['tags']['group_friendly'] ?? false))
                    ->sortByDesc(fn($item) => sprintf(
                        '%d-%010d-%010d',
                        $item['is_featured'] ? 1 : 0,
                        $item['views'],
                        $item['id']
                    ))
                    ->take(6)
            ),
            $this->buildSection(
                key: 'luxury_hotels',
                title: 'Khách sạn cao cấp',
                subtitle: 'Khách sạn và resort nổi bật',
                items: $stays
                    ->filter(fn($item) => $item['type'] === 'hotel')
                    ->sortByDesc(fn($item) => sprintf(
                        '%02d-%d-%010d-%010d',
                        $item['level_star'],
                        $item['is_featured'] ? 1 : 0,
                        $item['views'],
                        $item['id']
                    ))
                    ->take(6)
            ),
        ];

        $sections = collect($sections)
            ->filter(fn($section) => ! empty($section['items']))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Home data fetched successfully.',
            'data' => [
                'banners' => [],
                'categories' => $stayCategories->values(),
                'services' => $serviceCategories->values(),
                'featured_stays' => $featuredStays,
                'sections' => $sections,
                'products' => $normalProducts->take(6)->values(),
                'news' => $news,
            ],
        ]);
    }

    private function getStayCategories(): Collection
    {
        $latestCategorySlugs = $this->latestSlugSubquery('Botble\\Ecommerce\\Models\\ProductCategory');

        return DB::table('ec_product_categories as c')
            ->leftJoinSub($latestCategorySlugs, 's', function ($join) {
                $join->on('s.reference_id', '=', 'c.id');
            })
            ->where('c.status', 'published')
            ->where(function ($query) {
                $query->where('c.parent_id', 0)
                    ->orWhereNull('c.parent_id');
            })
            ->orderBy('c.id')
            ->limit(8)
            ->select([
                'c.id',
                'c.name',
                'c.image',
                'c.icon_image',
                'c.icon',
                'c.description',
                'c.order',
                's.slug_key as slug',
            ])
            ->get()
            ->map(fn($item) => [
                'id' => (int) $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'description' => Str::limit(strip_tags((string) $item->description), 140),
                'image' => $this->formatImageUrl($item->image ?: $item->icon_image ?: $item->icon),
                'icon' => $this->formatImageUrl($item->icon_image ?: $item->icon ?: $item->image),
            ])
            ->values();
    }

    private function getServiceCategories(): Collection
    {
        $latestCategorySlugs = $this->latestSlugSubquery('Botble\\Ecommerce\\Models\\ProductCategory');

        $serviceParentIds = DB::table('ec_product_categories')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('name', 'like', '%dịch vụ%')
                    ->orWhere('name', 'like', '%dich vu%');
            })
            ->pluck('id');

        return DB::table('ec_product_categories as c')
            ->leftJoinSub($latestCategorySlugs, 's', function ($join) {
                $join->on('s.reference_id', '=', 'c.id');
            })
            ->where('c.status', 'published')
            ->when(
                $serviceParentIds->isNotEmpty(),
                fn($query) => $query->whereIn('c.parent_id', $serviceParentIds->all()),
                fn($query) => $query->whereIn('c.name', [
                    'Siêu Thị',
                    'Giặt Sấy',
                    'Đặt Tiệc',
                    'Dịch vụ chụp ảnh',
                    'Thuê Xe',
                    'Minibar',
                    'Bữa Ăn',
                    'Dọn Phòng',
                    'Dịch vụ miễn phí',
                    'Game',
                    'Dịch vụ đặc biệt',
                    'Tour Vé',
                    'Spa In Room',
                ])
            )
            ->select([
                'c.id',
                'c.name',
                'c.image',
                'c.icon_image',
                'c.icon',
                'c.order',
                's.slug_key as slug',
            ])
            ->orderByDesc('c.order')
            ->get()
            ->unique('id')
            ->map(fn($item) => [
                'id' => (int) $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'icon' => $this->formatImageUrl($item->icon_image ?: $item->icon ?: $item->image),
                'image' => $this->formatImageUrl($item->image ?: $item->icon_image ?: $item->icon),
            ])
            ->values();
    }

    private function getStayProducts(): Collection
    {
        $latestProductSlugs = $this->latestSlugSubquery('Botble\\Ecommerce\\Models\\Product');

        $rows = DB::table('ec_products as p')
            ->leftJoinSub($latestProductSlugs, 's', function ($join) {
                $join->on('s.reference_id', '=', 'p.id');
            })
            ->where('p.is_variation', 0)
            ->where('p.status', 'published')
            ->whereIn('p.type', ['hotel', 'villa'])
            ->where(function ($query) {
                $query->where('p.is_tour', 0)
                    ->orWhereNull('p.is_tour');
            })
            ->where(function ($query) {
                $query->whereNull('p.sku')
                    ->orWhere('p.sku', 'not like', 'TOUR%');
            })
            ->where(function ($query) {
                $query->where('p.name', 'not like', '%tour%')
                    ->where('p.name', 'not like', '%combo%')
                    ->where('p.name', 'not like', '%spa%')
                    ->where('p.name', 'not like', '%check-in%')
                    ->where('p.name', 'not like', '%vé máy bay%')
                    ->where('p.name', 'not like', '%du thuyền%');
            })
            ->select([
                'p.id',
                'p.name',
                'p.description',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.thumbnail',
                'p.images',
                'p.address',
                'p.views',
                'p.type',
                'p.level_star',
                'p.is_featured',
                's.slug_key as slug',
            ])
            ->orderByDesc('p.id')
            ->get();

        $productIds = $rows->pluck('id')->map(fn($id) => (int) $id)->all();
        $metaMap = $this->getMetaMapByProductIds($productIds);

        return $rows->map(function ($item) use ($metaMap) {
            $productId = (int) $item->id;
            $meta = $metaMap[$productId] ?? [];
            $gallery = $this->parseGallery($item->images);
            $thumbnail = $this->resolveThumbnail(
                thumbnail: $item->thumbnail ?? null,
                image: $item->image ?? null,
                gallery: $gallery,
            );

            $maxAdults = $this->toInt($meta['max_adults'] ?? null);
            $maxChildren = $this->toInt($meta['max_children'] ?? null);
            $beds = $this->toInt($meta['beds'] ?? null);
            $size = $this->normalizeMetaValue($meta['size'] ?? null);

            return [
                'id' => $productId,
                'name' => $item->name,
                'slug' => $item->slug,
                'type' => $item->type,
                'location' => $item->address ?? '',
                'description' => Str::limit(strip_tags((string) ($item->description ?? '')), 120),
                'thumbnail' => $thumbnail,
                'image' => $this->formatImageUrl($item->image ?? null) ?? $thumbnail,
                'price' => (float) ($item->price ?? 0),
                'sale_price' => (float) ($item->sale_price ?? 0),
                'display_price' => (float) (($item->sale_price ?: $item->price) ?? 0),
                'views' => (int) ($item->views ?? 0),
                'level_star' => (int) ($item->level_star ?? 0),
                'is_featured' => (bool) $item->is_featured,
                'max_adults' => $maxAdults,
                'max_children' => $maxChildren,
                'beds' => $beds,
                'size' => $size,
                'tags' => $this->buildListingTags(
                    name: $item->name,
                    address: $item->address,
                    description: $item->description,
                    maxAdults: $maxAdults,
                ),
            ];
        })->values();
    }

    private function getNormalProducts(): Collection
    {
        $latestProductSlugs = $this->latestSlugSubquery('Botble\\Ecommerce\\Models\\Product');

        return DB::table('ec_products as p')
            ->leftJoinSub($latestProductSlugs, 's', function ($join) {
                $join->on('s.reference_id', '=', 'p.id');
            })
            ->where('p.is_variation', 0)
            ->where('p.status', 'published')
            ->whereNotIn('p.type', ['hotel', 'villa'])
            ->select([
                'p.id',
                'p.name',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.thumbnail',
                'p.images',
                'p.description',
                'p.type',
                's.slug_key as slug',
            ])
            ->orderByDesc('p.id')
            ->get()
            ->map(function ($item) {
                $gallery = $this->parseGallery($item->images);
                $thumbnail = $this->resolveThumbnail(
                    thumbnail: $item->thumbnail ?? null,
                    image: $item->image ?? null,
                    gallery: $gallery,
                );

                return [
                    'id' => (int) $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'type' => $item->type,
                    'description' => Str::limit(strip_tags((string) ($item->description ?? '')), 100),
                    'thumbnail' => $thumbnail,
                    'image' => $this->formatImageUrl($item->image ?? null) ?? $thumbnail,
                    'price' => (float) ($item->price ?? 0),
                    'sale_price' => (float) ($item->sale_price ?? 0),
                ];
            })
            ->values();
    }

    private function getFeaturedNews(): Collection
    {
        $latestPostSlugs = $this->latestNewsSlugSubquery('Botble\\Blog\\Models\\Post');

        $posts = DB::table('posts as p')
            ->leftJoinSub($latestPostSlugs, 'ps', function ($join) {
                $join->on('ps.reference_id', '=', 'p.id');
            })
            ->where('p.status', 'published')
            ->select([
                'p.id',
                'p.name',
                'p.description',
                'p.content',
                'p.image',
                'p.created_at',
                'p.is_featured',
                'ps.slug_key as slug',
            ])
            ->orderByDesc('p.created_at')
            ->orderByDesc('p.id')
            ->limit(8)
            ->get();

        $postIds = $posts->pluck('id')->map(fn($id) => (int) $id)->all();
        $categoryMap = $this->getNewsCategoryMapByPostIds($postIds);

        return $posts->map(function ($item) use ($categoryMap) {
            $categories = $categoryMap[(int) $item->id] ?? [];
            $primaryCategory = $categories[0] ?? null;

            return [
                'id' => (int) $item->id,
                'title' => $item->name,
                'slug' => $item->slug,
                'excerpt' => Str::limit(
                    trim((string) ($item->description ?: strip_tags((string) $item->content))),
                    140
                ),
                'image' => $this->formatImageUrl($item->image),
                'published_at' => $item->created_at,
                'published_date_label' => $item->created_at
                    ? date('d/m/Y', strtotime((string) $item->created_at))
                    : null,
                'is_featured' => (bool) ($item->is_featured ?? false),
                'category' => $primaryCategory,
            ];
        })->values();
    }

    private function getNewsCategoryMapByPostIds(array $postIds): array
    {
        if (empty($postIds)) {
            return [];
        }

        $latestCategorySlugs = $this->latestNewsSlugSubquery('Botble\\Blog\\Models\\Category');

        $rows = DB::table('post_categories as pc')
            ->join('categories as c', 'c.id', '=', 'pc.category_id')
            ->leftJoinSub($latestCategorySlugs, 'cs', function ($join) {
                $join->on('cs.reference_id', '=', 'c.id');
            })
            ->whereIn('pc.post_id', $postIds)
            ->where('c.status', 'published')
            ->select([
                'pc.post_id',
                'c.id',
                'c.name',
                'c.description',
                'cs.slug_key as slug',
            ])
            ->orderBy('c.id')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $postId = (int) $row->post_id;
            if (!isset($result[$postId])) {
                $result[$postId] = [];
            }

            $result[$postId][] = [
                'id' => (int) $row->id,
                'name' => $row->name,
                'slug' => $row->slug,
                'description' => $row->description,
            ];
        }

        return $result;
    }

    private function buildSection(
        string $key,
        string $title,
        string $subtitle,
        Collection $items
    ): array {
        return [
            'key' => $key,
            'title' => $title,
            'subtitle' => $subtitle,
            'items' => $items->values()->all(),
        ];
    }

    private function buildListingTags(
        ?string $name,
        ?string $address,
        ?string $description,
        int $maxAdults
    ): array {
        $text = Str::lower(trim(
            ($name ?? '') . ' ' .
                ($address ?? '') . ' ' .
                strip_tags((string) ($description ?? ''))
        ));

        $containsAny = function (array $keywords) use ($text): bool {
            foreach ($keywords as $keyword) {
                if (Str::contains($text, Str::lower($keyword))) {
                    return true;
                }
            }

            return false;
        };

        return [
            'beachfront' => $containsAny(['beach', 'biển', 'ocean', 'sea', 'sát biển', 'ven biển', 'mỹ khê']),
            'private_pool' => $containsAny(['pool', 'hồ bơi', 'private pool']),
            'family_friendly' => $maxAdults >= 4,
            'group_friendly' => $maxAdults >= 8,
        ];
    }

    private function getMetaMapByProductIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $rows = DB::table('meta_boxes')
            ->where('reference_type', 'Botble\\Ecommerce\\Models\\Product')
            ->whereIn('reference_id', $productIds)
            ->select(['reference_id', 'meta_key', 'meta_value'])
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $referenceId = (int) $row->reference_id;
            if (!isset($result[$referenceId])) {
                $result[$referenceId] = [];
            }
            $result[$referenceId][$row->meta_key] = $row->meta_value;
        }

        return $result;
    }

    private function parseGallery(?string $images): array
    {
        if (!$images) {
            return [];
        }

        $decoded = json_decode($images, true);

        if (!is_array($decoded)) {
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

    private function normalizeMetaValue(mixed $value): mixed
    {
        if ($value === null || $value === '' || $value === '[null]') {
            return null;
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded) && count($decoded) === 1) {
                return $decoded[0];
            }

            return $decoded;
        }

        return $value;
    }

    private function toInt(mixed $value): int
    {
        $normalized = $this->normalizeMetaValue($value);

        if ($normalized === null || $normalized === '') {
            return 0;
        }

        if (is_array($normalized)) {
            $normalized = $normalized[0] ?? 0;
        }

        return (int) $normalized;
    }

    private function latestSlugSubquery(string $referenceType)
    {
        return DB::table('slugs as s1')
            ->join(
                DB::raw('(SELECT MAX(id) as max_id, reference_id FROM slugs WHERE reference_type = "' . addslashes($referenceType) . '" GROUP BY reference_id) as latest'),
                'latest.max_id',
                '=',
                's1.id'
            )
            ->select([
                's1.reference_id',
                's1.key as slug_key',
            ]);
    }

    private function latestNewsSlugSubquery(string $referenceType)
    {
        return DB::table('slugs as s1')
            ->join(
                DB::raw('(SELECT MAX(id) as max_id, reference_id FROM slugs WHERE reference_type = "' . addslashes($referenceType) . '" GROUP BY reference_id) as latest'),
                'latest.max_id',
                '=',
                's1.id'
            )
            ->select([
                's1.reference_id',
                's1.key as slug_key',
            ]);
    }

    private function formatImageUrl(?string $image): ?string
    {
        if (!$image) {
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
