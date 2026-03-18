@php
    $sortedAttributeSets = $productAttributeSets->sort(function ($a, $b) {
        $order = ['date-range' => 1, 'default' => 2];
        $aOrder = $order[$a->display_layout] ?? $order['default'];
        $bOrder = $order[$b->display_layout] ?? $order['default'];
        return $aOrder - $bOrder;
    });
@endphp

<div class="row">
    @foreach ($sortedAttributeSets as $attributeSet)
        @if($attributeSet->display_layout == "date-range")
            @if($product)

                <div class="col-md-4 col-sm-6">
                    <x-core::form-group>
                        <x-core::form.label for="attribute-{{ $attributeSet->slug }}" class="required">
                            {{ $attributeSet->title }}
                        </x-core::form.label>

                        @php
                            if ($selected = $productVariationsInfo ? $productVariationsInfo->firstWhere('attribute_set_id', $attributeSet->id) : null) {
                                $selected = [$selected->id => $selected->title];
                            } else {
                                $selected = ['' => '-- ' . trans('plugins/ecommerce::products.select') . ' --'];
                            }
                        @endphp

                        <x-core::form.select
                            name="attribute_sets[{{ $attributeSet->id }}]"
                            :value="Arr::first(array_keys($selected))"
                            :options="$selected"
                            :data-id="$attributeSet->id"
                            class="select-attributes"/>
                    </x-core::form-group>
                </div>
            @else
                <div class="col-md-6 col-sm-6">
                    <x-core::form-group>
                        <x-core::form.label>Ngày bắt đầu</x-core::form.label>
                        {!! Form::datePicker("attribute_sets[$attributeSet->id][start_date]", null, [
                            'class' => 'form-control',
                            'data-date-format' => 'd-m-Y'
                        ]) !!}
                    </x-core::form-group>
                </div>
                <div class="col-md-6 col-sm-6">
                    <x-core::form-group>
                        <x-core::form.label>Ngày kết thúc</x-core::form.label>
                        {!! Form::datePicker("attribute_sets[$attributeSet->id][end_date]", null, [
                            'class' => 'form-control',
                            'data-date-format' => 'd-m-Y'
                        ]) !!}
                    </x-core::form-group>
                </div>
                <div class="col-md-12 col-sm-12">
                    <x-core::form-group>
                        <x-core::form.label>{{ $attributeSet->title }}</x-core::form.label>
                        <div class="form-check-inline d-flex gap-3">
                            @foreach(['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday'] as $key => $day)
                                <label class="form-check-label me-3">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="attribute_sets[{{ $attributeSet->id }}][day-of-week][]"
                                           value="{{ $key }}"> {{ $day }}
                                </label>
                            @endforeach
                        </div>
                    </x-core::form-group>
                </div>
            @endif
        @else
            <div class="col-md-4 col-sm-6">
                <x-core::form-group>
                    <x-core::form.label for="attribute-{{ $attributeSet->slug }}" class="required">
                        {{ $attributeSet->title }}
                    </x-core::form.label>

                    @php
                        if ($selected = $productVariationsInfo ? $productVariationsInfo->firstWhere('attribute_set_id', $attributeSet->id) : null) {
                            $selected = [$selected->id => $selected->title];
                        } else {
                            $selected = ['' => '-- ' . trans('plugins/ecommerce::products.select') . ' --'];
                        }
                    @endphp

                    <x-core::form.select
                        name="attribute_sets[{{ $attributeSet->id }}]"
                        :value="Arr::first(array_keys($selected))"
                        :options="$selected"
                        :data-id="$attributeSet->id"
                        class="select-attributes"/>
                </x-core::form-group>
            </div>
        @endif
    @endforeach
</div>
