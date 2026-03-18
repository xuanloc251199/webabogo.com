<div class="mb-3">
    <label class="form-label" for="beds" class="form-label">{{ __('Beds') }}</label>
    {!! Form::number('beds', $beds, ['class' => 'form-control', 'id' => 'beds']) !!}
</div>

<div class="mb-3">
    <label class="form-label" for="max_adults" class="form-label">{{ __('Max Adults') }}</label>
    {!! Form::number('max_adults', $maxAdults, ['class' => 'form-control', 'id' => 'max_adults']) !!}
</div>
<div class="mb-3">
    <label class="form-label" for="max_children" class="form-label">{{ __('Max Children') }}</label>
    {!! Form::number('max_children', $maxChildren, ['class' => 'form-control', 'id' => 'max_children']) !!}
</div>
<div class="mb-3">
    <label class="form-label" for="children_surplus_fee" class="form-label">{{ __('Phụ thu trẻ em') }}</label>
    {!! Form::number('children_surplus_fee', $childrenSurplusFee, ['class' => 'form-control', 'id' => 'children_surplus_fee']) !!}
</div>

<div class="mb-3">
    <label class="form-label" for="size" class="form-label">{{ __('Size') }}</label>
    {!! Form::number('size', $size, ['class' => 'form-control', 'id' => 'size']) !!}
</div>
<div class="mb-3">
    <x-core::form.text-input
        :label="trans('Addon Fee')"
        name="addonFee"
        :data-thousands-separator="EcommerceHelper::getThousandSeparatorForInputMask()"
        :data-decimal-separator="EcommerceHelper::getDecimalSeparatorForInputMask()"
        :value="old('addonFee', $addonFee ?? 0)"
        step="any"
        class="input-mask-number"
        :group-flat="true"
    >
        <x-slot:prepend>
            <span class="input-group-text">{{ get_application_currency()->symbol }}</span>
        </x-slot:prepend>
    </x-core::form.text-input>
</div>
<div class="mb-3">
    <x-core::form.text-input
        :label="trans('Service Fee')"
        name="serviceFee"
        :data-thousands-separator="EcommerceHelper::getThousandSeparatorForInputMask()"
        :data-decimal-separator="EcommerceHelper::getDecimalSeparatorForInputMask()"
        :value="old('serviceFee', $serviceFee ?? 0)"
        step="any"
        class="input-mask-number"
        :group-flat="true"
    >
        <x-slot:prepend>
            <span class="input-group-text">{{ get_application_currency()->symbol }}</span>
        </x-slot:prepend>
    </x-core::form.text-input>
</div>
