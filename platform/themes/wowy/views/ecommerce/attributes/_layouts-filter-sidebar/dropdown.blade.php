<div class="sidebar-widget widget_categories mb-30 p-20 bg-grey border-radius-10 widget-filter-item" data-type="dropdown">
    <div class="widget-header position-relative mb-20 pb-10">
        <h5 class="widget-title mb-10" data-title="{{ $set->title }}" >{{ __('By :name', ['name' => $set->title]) }}</h5>
        <div class="bt-1 border-color-1"></div>
    </div>

    <div class="custome-checkbox">
        <div class="attribute-values">
            <div class="dropdown-swatch">
                <label>
                    <select class="form-select product-filter-item" name="attributes[{{ $set->slug }}][]">
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach($attributes->where('attribute_set_id', $set->id) as $attribute)
                            <option value="{{ $attribute->id }}" {{ in_array($attribute->id, $selected) ? 'selected' : '' }}>{{ $attribute->title }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>
    </div>
</div>
