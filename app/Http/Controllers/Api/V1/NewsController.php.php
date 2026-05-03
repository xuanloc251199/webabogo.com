<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        $keyword = trim((string) $request->get('keyword', ''));
        $categorySlug = trim((string) $request->get('category', ''));

        if ($perPage <= 0) {
            $perPage = 10;
        }

        if ($perPage > 30) {
            $perPage = 30;
        }

        if ($page <= 0) {
            $page = 1;
        }

        $latestPostSlugs = $this->latestSlugSubquery('Botble\\Blog\\Models\\Post');
        $latestCategorySlugs = $this->latestSlugSubquery('Botble\\Blog\\Models\\Category');

        $query = DB::table('posts as p')
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
            ]);

        if ($keyword !== '') {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('p.name', 'like', "%{$keyword}%")
                    ->orWhere('p.description', 'like', "%{$keyword}%")
                    ->orWhere('p.content', 'like', "%{$keyword}%");
            });
        }

        if ($categorySlug !== '') {
            $query->whereExists(function ($subQuery) use ($categorySlug, $latestCategorySlugs) {
                $subQuery->select(DB::raw(1))
                    ->from('post_categories as pc')
                    ->join('categories as c', 'c.id', '=', 'pc.category_id')
                    ->leftJoinSub($latestCategorySlugs, 'cs', function ($join) {
                        $join->on('cs.reference_id', '=', 'c.id');
                    })
                    ->whereColumn('pc.post_id', 'p.id')
                    ->where('c.status', 'published')
                    ->where('cs.slug_key', $categorySlug);
            });
        }

        $total = (clone $query)->count('p.id');

        $posts = $query
            ->orderByDesc('p.created_at')
            ->orderByDesc('p.id')
            ->forPage($page, $perPage)
            ->get();

        $postIds = $posts->pluck('id')->map(fn($id) => (int) $id)->all();
        $categoryMap = $this->getCategoryMapByPostIds($postIds);

        $items = $posts->map(function ($item) use ($categoryMap) {
            $categories = $categoryMap[(int) $item->id] ?? [];
            $primaryCategory = $categories[0] ?? null;

            return [
                'id' => (int) $item->id,
                'title' => $item->name,
                'slug' => $item->slug,
                'excerpt' => $this->buildExcerpt($item->description, $item->content),
                'image' => $this->formatImageUrl($item->image),
                'published_at' => $item->created_at,
                'published_date_label' => $item->created_at
                    ? date('d/m/Y', strtotime((string) $item->created_at))
                    : null,
                'is_featured' => (bool) ($item->is_featured ?? false),
                'category' => $primaryCategory,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'News fetched successfully.',
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
        $latestPostSlugs = $this->latestSlugSubquery('Botble\\Blog\\Models\\Post');

        $post = DB::table('posts as p')
            ->leftJoinSub($latestPostSlugs, 'ps', function ($join) {
                $join->on('ps.reference_id', '=', 'p.id');
            })
            ->where('p.status', 'published')
            ->where('ps.slug_key', $slug)
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
            ->first();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'News not found.',
            ], 404);
        }

        $categoryMap = $this->getCategoryMapByPostIds([(int) $post->id]);
        $categories = $categoryMap[(int) $post->id] ?? [];
        $primaryCategory = $categories[0] ?? null;

        return response()->json([
            'success' => true,
            'message' => 'News detail fetched successfully.',
            'data' => [
                'id' => (int) $post->id,
                'title' => $post->name,
                'slug' => $post->slug,
                'excerpt' => $this->buildExcerpt($post->description, $post->content),
                'description' => $post->description,
                'content' => $post->content,
                'image' => $this->formatImageUrl($post->image),
                'published_at' => $post->created_at,
                'published_date_label' => $post->created_at
                    ? date('d/m/Y', strtotime((string) $post->created_at))
                    : null,
                'is_featured' => (bool) ($post->is_featured ?? false),
                'category' => $primaryCategory,
                'categories' => $categories,
            ],
        ]);
    }

    private function getCategoryMapByPostIds(array $postIds): array
    {
        if (empty($postIds)) {
            return [];
        }

        $latestCategorySlugs = $this->latestSlugSubquery('Botble\\Blog\\Models\\Category');

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

    private function buildExcerpt(?string $description, ?string $content): string
    {
        $base = trim((string) ($description ?: strip_tags((string) $content)));
        return Str::limit($base, 140);
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
