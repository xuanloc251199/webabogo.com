<?php

namespace Botble\Place\Services;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\Helper;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Media\Facades\RvMedia;
use Botble\Place\Models\Place;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Models\Slug;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PlaceService
{
    public function handleFrontRoutes(Slug|array $slug): Slug|array|Builder
    {
        if (!$slug instanceof Slug) {
            return $slug;
        }

        $condition = [
            'id' => $slug->reference_id,
            'status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::guard()->check() && request()->input('preview')) {
            Arr::forget($condition, 'status');
        }

        switch ($slug->reference_type) {
            case Place::class:

                /**
                 * @var Post $post
                 */
                $place = Place::query()
                    ->where($condition)
                    ->with(['slugable'])
                    ->firstOrFail();

                SeoHelper::setTitle($place->name)
                    ->setDescription($place->description);

                $meta = new SeoOpenGraph();
                if ($place->image) {
                    $meta->setImage(RvMedia::getImageUrl($place->image));
                }
                $meta->setDescription($place->description);
                $meta->setUrl($place->url);
                $meta->setTitle($place->name);
                $meta->setType('place');

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($place->url);

                if (function_exists('admin_bar')) {
                    AdminBar::registerLink(
                        trans('plugins/place::place.edit_this_place'),
                        route('place.edit', $place->getKey()),
                        null,
                        'place.edit'
                    );
                }

                if (function_exists('shortcode')) {
                    shortcode()->getCompiler()->setEditLink(route('place.edit', $place->id), 'place.edit');
                }
                Theme::breadcrumb()->add($place->name, $place->url);

                $products = $place->products()
                    ->where("ec_products.is_variation", 0)
                    ->withAvg('reviews', 'star')
                    ->orderBy('reviews_avg_star', 'DESC')
                    ->orderBy('ec_products.order', 'ASC')
                    ->orderBy('ec_products.created_at', 'DESC')
                    ->with("slugable")
                    ->paginate(8);
                $posts = $place->posts()->orderBy("posts.created_at", "desc")->limit(8)->get();
                return [
                    'view' => 'place',
                    'default_view' => 'plugins/place::themes.place',
                    'data' => compact('place', "products", "posts"),
                    'slug' => $place->slug,
                ];
        }

        return $slug;
    }
}
