@php
    $isDisplayPriceOriginal ??= true;
    $priceWrapperClassName ??= null;
    $priceClassName ??= null;
    $priceOriginalClassName ??= null;
    $priceOriginalWrapperClassName ??= null;
@endphp

<div class="{{ $priceWrapperClassName === null ? 'bb-product-price mb-3' : $priceWrapperClassName }}">
    <span
        class="{{ $priceClassName === null ? 'bb-product-price-text fw-bold' : $priceClassName }}"
        data-bb-value="product-price"
    >
        @if(function_exists('get_current_date_price'))
            {{ format_price(get_current_date_price($product)) }}
        @else
            {{ $product->price()->displayAsText() }}
        @endif
    </span>

    @if($isDisplayPriceOriginal && $product->isOnSale())
        @include(EcommerceHelper::viewPath('includes.product-prices.original'), [
            'priceWrapperClassName' => $priceOriginalWrapperClassName,
            'priceClassName' => $priceOriginalClassName,
        ])
    @endif
</div>
