@if ($product)
    <div class="product-item-v2 bg-white rounded-20 shadow-sm">
        <a href="{{ $product->url }}">
            <img src="{{ RvMedia::getImageUrl($product->image,null,false, RvMedia::getDefaultImage()) }}"
                 class="img-fluid top-rounded-20 w-100"
                 alt="{{ $product->name }}">
        </a>
        <div class="p-3">
            <a href="{{ $product->url }}" class="fw-600 fs-16 text-decoration-none text-dark max-2-lines title-product">
                {{ $product->name }}
            </a>
            @include(Theme::getThemeNamespace() . '::views.ecommerce.level-star', compact('product'))
            <div class="d-flex justify-content-between align-items-center">

                <p class="text-muted mb-0 fs-14 item-address">
                    <i class="fa-solid fa-map-marker-alt"></i> {{$product->address}}
                </p>

                <span class="product-price-badge fs-16 fw-700">
                    @php
                        $currentDatePrice = get_min_price_product_group($product);
                    @endphp
                    {{ format_price($currentDatePrice) }}
                </span>
            </div>
        </div>
    </div>
@endif
