{!! do_shortcode('[product-overview-search place_id='.$place->id.'][/product-overview-search]') !!}
{!! do_shortcode('[product-overview-category][/product-overview-category]') !!}
<div class="product-list">
    <div class="container py-4">
        <h3 class="mb-3 fw-bold">{{ 'Sản phẩm' }}</h3>
        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-items', compact('products'))
    </div>
</div>
@if($posts->count()>0)
    <div class="section-4">
        <!-- Khối Tin tức -->
        <div class="container py-4">
            <h3 class="mb-3 fw-bold">Tin tức mới nhất</h3>
            <!-- Swiper Tin tức -->
            <div class="swiper blog-swiper">
                <div class="swiper-wrapper category-filter-content">
                    @include(Theme::getThemeNamespace('partials.shortcodes.includes.category-filter-item'),['posts' => $posts])
                </div>
            </div>

            @if($posts->count()>5)
            <div class="text-center mt-3">
                <a href="{{ route('public.search',['type' => 'recent',"place"=>$place->id]) }}"
                   class="btn btn-main-color px-5">Xem Tất Cả</a>
            </div>
            @endif
        </div>
    </div>
@endif


