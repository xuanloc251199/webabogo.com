<input type="hidden" name="page" data-value="{{ $products->currentPage() }}">
{{--<input type="hidden" name="sort-by" value="{{ BaseHelper::stringify(request()->input('sort-by')) }}">--}}
<input type="hidden" name="num" value="{{ BaseHelper::stringify(request()->input('num')) }}">
<input type="hidden" name="q" value="{{ BaseHelper::stringify(request()->input('q')) }}">

<div class="row">
    @forelse ($products as $product)
        <div class="col-12 col-md-3 mb-3">
            @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', compact('product'))
        </div>
    @empty
        <div class="mt__60 mb__60 text-center">
            <p>{{ __('No products found!') }}</p>
        </div>
    @endforelse
</div>

@if ($products->hasPages())
    <br>
{{--    {!! $products->withQueryString()->links(Theme::getThemeNamespace() . '::partials.custom-pagination') !!}--}}
    {!! $products->withQueryString()->links(Theme::getThemeNamespace('partials.custom-pagination')) !!}
@endif
