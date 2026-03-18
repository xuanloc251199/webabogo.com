<div class="sidebar-widget widget_categories mb-30 p-20 bg-grey border-radius-10 widget-filter-item" data-type="text">
    <div class="widget-header position-relative mb-20 pb-10">
        <h5 class="widget-title mb-10" data-title="{{ $set->title }}" >{{ __('By :name', ['name' => $set->title]) }}</h5>
        <div class="bt-1 border-color-1"></div>
    </div>

    <ul class="list-filter size-filter font-small">
        @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
            <li data-slug="{{ $attribute->slug }}">
                <label>
                    <input class="product-filter-item" type="checkbox" name="attributes[{{ $set->slug }}][]" value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'checked' : '' }}>
                    <span>{{ $attribute->title }}</span>
                </label>
            </li>
        @endforeach
    </ul>
</div>
