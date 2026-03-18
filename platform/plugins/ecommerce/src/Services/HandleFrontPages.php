<?php

namespace Botble\Ecommerce\Services;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Ecommerce\Events\ProductViewed;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\GoogleAnalytics\GoogleTagManager;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Ecommerce\Services\Products\ProductCrossSalePriceService;
use Botble\Ecommerce\Services\Products\UpdateDefaultProductService;
use Botble\Ecommerce\Traits\CheckReviewConditionTrait;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Entities\Twitter\Card;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Models\Slug;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Botble\Ecommerce\Models\ProductVariationItem;

class HandleFrontPages
{
    use CheckReviewConditionTrait;

    public function __construct(
        protected ProductCrossSalePriceService $productCrossSalePriceService
    )
    {
    }

    public function handle(Slug|array $slug): array|Slug
    {
        if (!$slug instanceof Slug) {
            return $slug;
        }

        $request = request();

        $response = BaseHttpResponse::make();

        $isPreview = Auth::guard()->check() && $request->input('preview');

        switch ($slug->reference_type) {
            case Product::class:

                $condition = [
                    'ec_products.id' => $slug->reference_id,
                    'ec_products.status' => BaseStatusEnum::PUBLISHED,
                ];

                if ($isPreview) {
                    $condition['ec_products.status'] = BaseStatusEnum::PENDING;
                }

                $product = get_products(
                    [
                        'condition' => $condition,
                        'take' => 1,
                        'with' => [
                            'slugable',
                            'groupedProduct',
                            'tags',
                            'tags.slugable',
                            'categories',
                            'categories.slugable',
                            'options',
                            'options.values',
                            'crossSales' => function (BelongsToMany $query) {
                                $query->where('ec_product_cross_sale_relations.is_variant', false);
                            },
                        ],
                        ...EcommerceHelper::withReviewsParams(),
                    ]
                );

                if (!$product) {
                    abort(404);
                }

                $this->productCrossSalePriceService->applyProduct($product);

                SeoHelper::setTitle($product->name)->setDescription($product->description);

                $meta = new SeoOpenGraph();
                if ($product->image) {
                    $meta->setImage(RvMedia::getImageUrl($product->image));
                }
                $meta->setDescription($product->description);
                $meta->setUrl($product->url);
                $meta->setTitle($product->name);

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($product->url);

                $card = new Card();
                $card->setType(Card::TYPE_PRODUCT);
                $card->addMeta('label1', 'Price');
                $card->addMeta(
                    'data1',
                    format_price($product->front_sale_price_with_taxes) . ' ' . strtoupper(
                        get_application_currency()->title
                    )
                );
                $card->addMeta('label2', 'Website');
                $card->addMeta('data2', SeoHelper::openGraph()->getProperty('site_name'));
                $card->addMeta('domain', url(''));

                SeoHelper::twitter()->setCard($card);

                if (Helper::handleViewCount($product, 'viewed_product')) {
                    event(new ProductViewed($product, Carbon::now()));

                    EcommerceHelper::handleCustomerRecentlyViewedProduct($product);
                }

                Theme::breadcrumb()->add(__('Products'), route('public.products'));

                $category = $product->categories->sortByDesc('id')->first();

                if ($category) {
                    if ($category->parents->count()) {
                        foreach ($category->parents->reverse() as $parentCategory) {
                            Theme::breadcrumb()->add($parentCategory->name, $parentCategory->url);
                        }
                    }

                    Theme::breadcrumb()->add($category->name, $category->url);
                }

                Theme::breadcrumb()->add($product->name);

                Theme::addBodyAttributes(['class' => 'single-product']);

                if (function_exists('admin_bar')) {
                    admin_bar()
                        ->registerLink(
                            trans('plugins/ecommerce::products.edit_this_product'),
                            route('products.edit', $product->getKey()),
                            null,
                            'products.edit'
                        );
                }

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PRODUCT_MODULE_SCREEN_NAME, $product);

                app(GoogleTagManager::class)
                    ->viewItem($product);

                [$productImages, $productVariation, $selectedAttrs] = EcommerceHelper::getProductVariationInfo(
                    $product,
                    $request->input()
                );

                if (!$product->is_variation && $productVariation) {
                    $product = app(UpdateDefaultProductService::class)->updateColumns($product, $productVariation);
                    $selectedProductVariation = $productVariation->defaultVariation;
                    $selectedProductVariation->product_id = $productVariation->id;

                    $product->defaultVariation = $selectedProductVariation;

                    $product->image = $selectedProductVariation->configurableProduct->image ?: $product->image;
                }

                $checkReview = $this->checkReviewCondition($product->getKey());

                $startDate = Carbon::now()->format('d/m/Y');
                $endDate = Carbon::now()->addDay()->format('d/m/Y');
                $price = 0;
                $data["product"] = $product;
                $data["selectedAttrs"] = $selectedAttrs;
                $data["productImages"] = $productImages;
                $data["productVariation"] = $productVariation;
                $data["checkReview"] = $checkReview;
                if ($product->groupedProduct()->exists()) {
                    $view = 'ecommerce.product-group';
                } else {
                    if ($product->type == "tour") {
                        $data = array_merge($data, $this->redirectProductTour($product));

                        return [
                            'view' => 'ecommerce.product_tour',
                            'default_view' => 'plugins/ecommerce::themes.product',
                            'data' => $data,
                            'slug' => $product->slug,
                        ];
                    } elseif ($product->type == "villa") {

                        $cartItem = Cart::instance('cart')->get($request->get("rowId"));

                        if ($cartItem) {
                            $days = $cartItem->options->get('days', []);
                            $startDate = Carbon::parse(reset($days))->format('d/m/Y');
                            if (count($days) > 1) {
                                $endDate = Carbon::parse(end($days))->addDay()->format('d/m/Y');
                            } else {
                                $endDate = Carbon::parse(reset($days))->addDay()->format('d/m/Y');
                            }
                            $price = format_price($cartItem->qty * $cartItem->price);
                        } else {
                            $price = format_price(get_current_date_price($product));
                        }
                        $view = 'ecommerce.product_villa';
                    } else {
                        $productsGroupedParent = $product->productsGroupedParent->first();

                        $cartItem = Cart::instance('cart')->get($request->get("rowId"));

                        if ($cartItem) {
                            $days = $cartItem->options->get('days', []);
                            $startDate = Carbon::parse(reset($days))->format('d/m/Y');
                            if (count($days) > 1) {
                                $endDate = Carbon::parse(end($days))->addDay()->format('d/m/Y');
                            } else {
                                $endDate = Carbon::parse(reset($days))->addDay()->format('d/m/Y');
                            }
                            $price = format_price($cartItem->qty * $cartItem->price);
                            $adults = $cartItem->options->adults_number;
                            $children = $cartItem->options->children_number;
                        } else {
                            $price = format_price(get_current_date_price($product));
                            $adults = MetaBox::getMetaData($product, 'max_adults', true);
                            $children = MetaBox::getMetaData($product, 'max_children', true);
                        }
                        $data["adults_number"] = $adults;
                        $data["children_number"] = $children;
                        $data["productsGroupedParent"] = $productsGroupedParent;
                        $data["startDate"] = $startDate;
                        $data["endDate"] = $endDate;
                        $data["price"] = $price;

                        $view = 'ecommerce.product';

                        return [
                            'view' => $view,
                            'default_view' => 'plugins/ecommerce::themes.product',
                            'data' => $data,
                            'slug' => $product->slug,
                        ];
                    }

                }
                return [
                    'view' => $view,
                    'default_view' => 'plugins/ecommerce::themes.product',
                    'data' => compact('product', 'selectedAttrs', 'productImages', 'productVariation', 'checkReview', 'startDate', 'endDate', 'price'),
                    'slug' => $product->slug,
                ];

            case ProductCategory::class:
                $category = ProductCategory::query()
                    ->where('id', $slug->reference_id)
                    ->when(!$isPreview, function ($query) {
                        $query->wherePublished();
                    })
                    ->with(['slugable'])
                    ->firstOrFail();
                if (!EcommerceHelper::productFilterParamsValidated($request)) {
                    $request = request();
                }

                $with = EcommerceHelper::withProductEagerLoadingRelations();

                $categoryIds = [$category->getKey()];

                $children = $category->activeChildren;

                while ($children->isNotEmpty()) {
                    foreach ($children as $item) {
                        $categoryIds[] = $item->id;
                        $children = $item->activeChildren;
                    }
                }

                if (!$request->input('categories')) {
                    $request->merge(['categories' => $categoryIds]);
                }
                // Check if this is a hotel/villa category that needs availability filtering
                $isHotelCategory = $this->isHotelCategory($category);
                if ($isHotelCategory && ($request->input('start_date') || $request->input('end_date'))) {
                    $request->merge(['filter_by_availability' => true]);

                }
                $products = app(GetProductService::class)->getProduct($request, null, null, $with);


                $request->merge([
                    'categories' => array_merge($category->parents->pluck('id')->all(), $categoryIds),
                ]);

                SeoHelper::setTitle($category->name)->setDescription($category->description);

                $meta = new SeoOpenGraph();
                if ($category->image) {
                    $meta->setImage(RvMedia::getImageUrl($category->image));
                }

                $meta->setDescription($category->description);
                $meta->setUrl($category->url);
                $meta->setTitle($category->name);

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($category->url);

                if (function_exists('admin_bar')) {
                    AdminBar::registerLink(
                        trans('plugins/ecommerce::product-categories.edit_this_category'),
                        route('product-categories.edit', $category->getKey()),
                        null,
                        'product-categories.edit'
                    );
                }

//                Theme::breadcrumb()->add(__('Products'), route('public.products'));

                if ($category->parents->isNotEmpty()) {
                    foreach ($category->parents->reverse() as $parentCategory) {
                        Theme::breadcrumb()->add($parentCategory->name, $parentCategory->url);
                    }
                }

                Theme::breadcrumb()->add($category->name);

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $category);
                $posts = $category->posts()->where("status", \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)->orderBy("id", "desc")->limit(10)->get();
                if ($request->ajax()) {
                    return $this->ajaxFilterProductsResponse($products, $response, $category);
                }

                return [
                    'view' => 'ecommerce.product-category',
                    'default_view' => 'plugins/ecommerce::themes.product-category',
                    'data' => compact('category', 'products', "posts"),
                    'slug' => $category->slug,
                ];

            case Brand::class:
                $brand = Brand::query()
                    ->where('id', $slug->reference_id)
                    ->when(!$isPreview, function ($query) {
                        $query->wherePublished();
                    })
                    ->with(['slugable'])
                    ->firstOrFail();

                if (!EcommerceHelper::productFilterParamsValidated($request)) {
                    $request = request();
                }

                $request->merge(['brands' => array_merge((array)request()->input('brands', []), [$brand->getKey()])]);

                $products = app(GetProductService::class)->getProduct(
                    $request,
                    null,
                    $brand->getKey(),
                    EcommerceHelper::withProductEagerLoadingRelations()
                );

                if ($request->ajax()) {
                    return $this->ajaxFilterProductsResponse($products, $response);
                }

                SeoHelper::setTitle($brand->name)->setDescription($brand->description);

                Theme::breadcrumb()->add($brand->name);

                $meta = new SeoOpenGraph();
                if ($brand->logo) {
                    $meta->setImage(RvMedia::getImageUrl($brand->logo));
                }
                $meta->setDescription($brand->description);
                $meta->setUrl($brand->url);
                $meta->setTitle($brand->name);

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($brand->url);

                if (function_exists('admin_bar')) {
                    admin_bar()
                        ->registerLink(
                            trans('plugins/ecommerce::brands.edit_this_brand'),
                            route('brands.edit', $brand->getKey()),
                            null,
                            'brands.edit'
                        );
                }

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, BRAND_MODULE_SCREEN_NAME, $brand);

                return [
                    'view' => 'ecommerce.brand',
                    'default_view' => 'plugins/ecommerce::themes.brand',
                    'data' => compact('brand', 'products'),
                    'slug' => $brand->slug,
                ];

            case ProductTag::class:
                $condition = [
                    'ec_product_tags.id' => $slug->reference_id,
                    'ec_product_tags.status' => BaseStatusEnum::PUBLISHED,
                ];

                if ($isPreview) {
                    Arr::forget($condition, 'ec_product_tags.status');
                }

                $tag = ProductTag::query()
                    ->with(['slugable', 'products'])
                    ->where($condition)
                    ->firstOrFail();

                if (!EcommerceHelper::productFilterParamsValidated($request)) {
                    $request = request();
                }

                $with = EcommerceHelper::withProductEagerLoadingRelations();

                $request->merge([
                    'tags' => [$tag->getKey()],
                ]);

                $products = app(GetProductService::class)->getProduct($request, null, null, $with);

                if ($request->ajax()) {
                    return $this->ajaxFilterProductsResponse($products, $response);
                }

                SeoHelper::setTitle($tag->name)->setDescription($tag->description);

                $meta = new SeoOpenGraph();
                $meta->setDescription($tag->description);
                $meta->setUrl($tag->url);
                $meta->setTitle($tag->name);

                SeoHelper::setSeoOpenGraph($meta);

                SeoHelper::meta()->setUrl($tag->url);

                Theme::breadcrumb()
                    ->add(__('Products'), route('public.products'))
                    ->add($tag->name);

                if (function_exists('admin_bar')) {
                    admin_bar()
                        ->registerLink(
                            trans('plugins/ecommerce::product-tag.edit_this_product_tag'),
                            route('product-tag.edit', $tag->getKey()),
                            null,
                            'product-tag.edit'
                        );
                }

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PRODUCT_TAG_MODULE_SCREEN_NAME, $tag);

                return [
                    'view' => 'ecommerce.product-tag',
                    'default_view' => 'plugins/ecommerce::themes.product-tag',
                    'data' => compact('tag', 'products'),
                    'slug' => $tag->slug,
                ];
        }

        return $slug;
    }

    private function redirectProductRoomHotel()
    {

    }

    private function redirectProductTour($product): array
    {
        $startDate = null;
        $price = 0;
        $price_adults = 0;
        $price_children = 0;
        $availableDates = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $product->id)
//            ->where('ec_product_attribute_sets.display_layout', "dropdown")
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.display_layout as attribute_set_display_layout',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.quantity'
            ])
            ->get()
            ->groupBy('variation_id');
        // Tổ chức dữ liệu theo format dễ sử dụng
        $tourDatesOld = [];
        foreach ($availableDates as $variation_id => $attr) {
            $attrDate = $attr->where("attribute_set_slug", "product-date-tour")->first();
            $attrAge = $attr->where("attribute_set_slug", "product-age")->first();
            if ($attrDate && $attrAge) {
                $key = Carbon::createFromFormat("d-m-Y", $attrDate->title)->toDateString();
                if ($attrAge->slug == "nguoi-lon") {
                    $tourDatesOld[$key] = [
                        'start' => Carbon::createFromFormat("d-m-Y", $attrDate->title)->toDateString(),
                        'startDate' => Carbon::createFromFormat("d-m-Y", $attrDate->title)->format('d/m/Y'),
                        'adult_variation_id' => $variation_id,
                        'title' => format_price($attrDate->sale_price ?: $attrDate->price),
                        'price_adults' => $attrDate->sale_price ?: $attrDate->price,
                        'format_price_adults' => format_price($attrDate->sale_price ?: $attrDate->price)
                    ];
                } else {
                    $tourDatesOld[$key]["children_variation_id"] = $variation_id;
                    $tourDatesOld[$key]["price_children"] = $attrDate->sale_price ?: $attrDate->price;
                    $tourDatesOld[$key]["format_price_children"] = format_price($attrDate->sale_price ?: $attrDate->price);
                    $tourDatesOld[$key]["totalPrice"] = format_price($tourDatesOld[$key]["price_children"] + $tourDatesOld[$key]["price_adults"]);

                }
            }

        }
        ksort($tourDatesOld);
        $tourDates = array_values($tourDatesOld);

        if (!empty($tourDates)) {
            $startDate = reset($tourDates)['startDate'] ?? "";
            $price = reset($tourDates)['title'] ?? "";
            $price_adults = reset($tourDates)['price_adults'] ?? 0;
            $price_children = reset($tourDates)['price_children'] ?? 0;
        }

        $adults_number = 1;
        $children_number = 0;
//
//
//        $cartItem = Cart::instance('cart')->get(request()->get("rowId"));
//
//        if ($cartItem) {
//            $startDate = $cartItem->options->start_date->format('d/m/Y');
//            $price = format_price($cartItem->qty * $cartItem->price);
//            $price_adults = $tourDatesOld[$cartItem->options->start_date->format('Y-m-d')]['price_adults'] ?? 0;
//            $price_children = $tourDatesOld[$cartItem->options->start_date->format('Y-m-d')]['price_children'] ?? 0;
//            $adults_number = $cartItem->options->adults_number ?? 0;
//            $children_number = $cartItem->options->children_number ?? 0;
//        }
        return [
            "tourDates" => $tourDates,
            "tourDatesOld" => $tourDatesOld,
            "startDate" => $startDate,
            "price" => $price,
            "price_adults" => $price_adults,
            "price_children" => $price_children,
            "adults_number" => $adults_number,
            "children_number" => $children_number,
        ];
    }

    private function redirectProductVilla()
    {

    }

    public function ajaxFilterProductsResponse(
        $products,
        BaseHttpResponse $response,
        ?ProductCategory $category = null
    ): array
    {
        $total = $products->total();
        $message = $total === 1 ? __(':total Product found', compact('total')) : __(
            ':total Products found',
            compact('total')
        );

        $data = view(EcommerceHelper::viewPath('includes.product-items'), compact('products'))->render();

        $breadcrumbView = Theme::getThemeNamespace('partials.breadcrumbs');

        if (view()->exists($breadcrumbView)) {
            $additional['breadcrumb'] = Theme::partial('breadcrumbs');
        } else {
            $additional['breadcrumb'] = Theme::breadcrumb()->render();
        }

        $filtersView = EcommerceHelper::viewPath('includes.filters');

        if (view()->exists($filtersView)) {
            $additional['filters_html'] = view($filtersView, compact('category'))->render();
        }

        return $response
            ->setData($data)
            ->setAdditional($additional)
            ->setMessage($message)
            ->toArray();
    }

    /**
     * Check if category is hotel/villa category that needs availability filtering
     */
    private function isHotelCategory(ProductCategory $category): bool
    {
        // Define hotel/villa category names or IDs
        $hotelCategoryNames = ['villa', 'hotel', 'resort', 'accommodation', 'khách sạn', 'khu nghỉ dưỡng'];

        // Check current category
        $categoryName = strtolower($category->name);
        foreach ($hotelCategoryNames as $hotelName) {
            if (str_contains($categoryName, $hotelName)) {
                return true;
            }
        }

        // Check parent categories
        foreach ($category->parents as $parent) {
            $parentName = strtolower($parent->name);
            foreach ($hotelCategoryNames as $hotelName) {
                if (str_contains($parentName, $hotelName)) {
                    return true;
                }
            }
        }

        return false;
    }
}
