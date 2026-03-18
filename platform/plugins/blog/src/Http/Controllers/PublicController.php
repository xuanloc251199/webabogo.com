<?php

namespace Botble\Blog\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Blog\Models\Category;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Place\Models\Place;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
    public function getSearch(Request $request, PostInterface $postRepository)
    {
        $title = '';
        $posts = null;

        if ($request->has('type')) {
            $type = $request->input('type');
            switch ($type) {
                case 'featured':
                    $posts = $postRepository->advancedGet(
                        [
                            'condition' => [
                                'status' => BaseStatusEnum::PUBLISHED,
                                'is_featured' => 1,
                            ],
                            'with' => ['slugable'],
                            'order_by' => [
                                'created_at' => 'desc',
                            ],
                            'paginate' => [
                                'per_page' => (int)theme_option('number_of_posts_in_a_category', 12),
                                'current_paged' => 1,
                            ],
                        ]
                    );
                    $title = 'Bài viết nổi bật';
                    break;
                case 'recent':
                    $posts = $postRepository->advancedGet(
                        [
                            'condition' => [
                                'status' => BaseStatusEnum::PUBLISHED,
                            ],
                            'with' => ['slugable'],
                            'order_by' => [
                                'created_at' => 'desc',
                            ],
                            'paginate' => [
                                'per_page' => (int)theme_option('number_of_posts_in_a_category', 12),
                                'current_paged' => 1,
                            ],
                            'select' => ['*'],
                        ]
                    );
                    $title = 'Bài viết gần đây';
                    break;
            }
        }

        if ($request->has('q') || $request->has('categories') || $request->has('places')) {
            $titleParts = [];

            if ($request->filled('q')) {
                $query = BaseHelper::stringify($request->input('q'));
                $titleParts[] = __('Keyword: ":query"', compact('query'));
            }

            if ($request->filled('categories')) {
                $categoryNames = Category::whereIn('id', (array)$request->input('categories'))
                    ->pluck('name')
                    ->toArray();
                if ($categoryNames) {
                    $titleParts[] = __('Categories: :categories', ['categories' => implode(', ', $categoryNames)]);
                }
            }

            if ($request->filled('places')) {
                $placeNames = Place::whereIn('id', (array)$request->input('places'))
                    ->pluck('name')
                    ->toArray();
                if ($placeNames) {
                    $titleParts[] = __('Places: :places', ['places' => implode(', ', $placeNames)]);
                }
            }
            $title = __('Search results') . ' - ' . implode(' | ', $titleParts);

            $posts = $postRepository->getFilters([
                'categories' => request()->input('categories'),
                'places' => $request->input('places'),
                'categories_exclude' => null,
                'exclude' => null,
                'include' => null,
                'author' => null,
                'author_exclude' => null,
                'featured' => null,
                'search' => $request->input('q'),
                'order_by' => 'updated_at',
                'order' => 'desc',
                'per_page' => (int)theme_option('number_of_posts_in_a_category', 12)
            ]);
        }

        SeoHelper::setTitle($title)
            ->setDescription($title);

        Theme::breadcrumb()->add($title, route('public.search'));

        return Theme::scope('search', compact('posts', 'title'))
            ->render();
    }
}
