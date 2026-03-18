<div class="section-3">
    <!-- Khối sản phẩm ưu đãi -->
    <div class="container py-4">
        <h3 class="mb-3 fw-bold">{{ $shortcode->title ?? 'Sản phẩm ưu đãi' }}</h3>
        <div class="swiper product-swiper">
            <div class="swiper-wrapper">

                <!-- Bắt đầu sản phẩm -->
                @foreach($products as $product)
                    <div class="swiper-slide p-1 p-md-0">
                        <div class="product-item-v2 bg-white rounded-20 shadow-sm">
                            <a href="{{ $product->url }}">
                                <img
                                    src="{{ RvMedia::getImageUrl($product->image,null,false, RvMedia::getDefaultImage()) }}"
                                    class="img-fluid top-rounded-20 w-100"
                                    alt="{{ $product->name }}">
                            </a>
                            <div class="p-3">
                                <a href="{{ $product->url }}"
                                   class="fw-600 fs-16 text-decoration-none text-dark max-2-lines title-product">
                                    {{ $product->name }}
                                </a>
                                @include(Theme::getThemeNamespace() . '::views.ecommerce.level-star', compact('product'))
                                <div class="d-flex justify-content-between align-items-center">

                                    <p class="text-muted mb-0 fs-14 item-address">
                                        <i class="fa-solid fa-map-marker-alt"></i> {{$product->address}}
                                    </p>
                                    <span class="product-price-badge fs-16 fw-700">
                                            {{ format_price(get_current_date_price($product)?:$product->front_sale_price) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Kết thúc sản phẩm -->

            </div>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('public.products') }}" class="btn btn-main-color px-5">Xem Tất Cả</a>
        </div>
    </div>
</div>
