@php
    $products->loadMissing(['categories', 'categories.slugable']);
    [$categories, $brands, $tags, $rand, $categoriesRequest, $urlCurrent, $categoryId, $maxFilterPrice] = EcommerceHelper::dataForFilter($category ?? null);
@endphp
{!! do_shortcode('[product-overview-search][/product-overview-search]') !!}
{!! do_shortcode('[product-overview-category][/product-overview-category]') !!}
<div class="container py-4">
    <div class="title">
        <h3 class="mb-3 fw-bold">{{ SeoHelper::getTitle() }}</h3>
    </div>
    <div class="row">
        <div class="products-listing position-relative">
            @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-items', compact('products'))
        </div>
    </div>
</div>
