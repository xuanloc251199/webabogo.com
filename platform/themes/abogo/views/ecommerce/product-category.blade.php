{!! do_shortcode('[product-overview-search route_action_form="'.$category->url.'"][/product-overview-search]') !!}
{!! do_shortcode("[product-overview-list-categories-children category_id=".$category->id."][/product-overview-list-categories-children]") !!}

<div class="product-list">
    <div class="container py-4">
        <div class="title">
            <h3 class="mb-3 fw-bold">{{ $category->name }}</h3>
        </div>
        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-items', compact('products'))
    </div>
</div>
@if($posts->count()>0)
    <div class="section-4">
        <!-- Khối Tin tức -->
        <div class="container py-4">
            <h3 class="mb-3 fw-bold">Tin tức {{ $category->name }}</h3>
            <!-- Swiper Tin tức -->

            <div class="swiper blog-swiper">
                <div class="swiper-wrapper category-filter-content">
                    @include(Theme::getThemeNamespace('partials.shortcodes.includes.category-filter-item'),['posts' => $posts])
                </div>
            </div>
            {{--        <div class="text-center mt-3">--}}
            {{--            <a href="{{ $category->url }}" class="btn btn-main-color px-5">Xem Tất Cả</a>--}}
            {{--        </div>--}}
        </div>
    </div>
@endif
