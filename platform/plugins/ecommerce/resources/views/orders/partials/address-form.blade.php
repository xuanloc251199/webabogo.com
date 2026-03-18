<div class="customer-address-payment-form">
    @if (EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check())
        <div class="mb-3 form-group">
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Login') }}</a></p>
        </div>
    @endif

    {!! apply_filters('ecommerce_checkout_address_form_before') !!}

    @auth('customer')
        <div class="mb-3 form-group">
            @if ($isAvailableAddress)
                <label
                    class="mb-2 form-label"
                    for="address_id"
                >{{ __('Select available addresses') }}:</label>
            @endif
            @php
                $oldSessionAddressId = old('address.address_id', $sessionAddressId);
            @endphp
            <div class="list-customer-address @if (!$isAvailableAddress) d-none @endif">
                <div class="select--arrow">
                    <select
                        class="form-control"
                        id="address_id"
                        name="address[address_id]"
                        @required($isAvailableAddress)
                    >
                        <option
                            value="new"
                            @selected($oldSessionAddressId == 'new')
                        >{{ __('Add new address...') }}</option>
                        @if ($isAvailableAddress)
                            @foreach ($addresses as $address)
                                <option
                                    value="{{ $address->id }}"
                                    @selected($oldSessionAddressId == $address->id)
                                >{{ $address->full_address }}</option>
                            @endforeach
                        @endif
                    </select>
                    <x-core::icon name="ti ti-chevron-down" />
                </div>
                <br>
                <div class="address-item-selected @if (!$sessionAddressId) d-none @endif">
                    @if ($isAvailableAddress && $oldSessionAddressId != 'new')
                        @if ($oldSessionAddressId && $addresses->contains('id', $oldSessionAddressId))
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $addresses->firstWhere('id', $oldSessionAddressId),
                            ])
                        @elseif ($defaultAddress = get_default_customer_address())
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $defaultAddress,
                            ])
                        @else
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => Arr::first($addresses),
                            ])
                        @endif
                    @endif
                </div>
                <div class="list-available-address d-none">
                    @if ($isAvailableAddress)
                        @foreach ($addresses as $address)
                            <div
                                class="address-item-wrapper"
                                data-id="{{ $address->id }}"
                            >
                                @include(
                                    'plugins/ecommerce::orders.partials.address-item',
                                    compact('address'))
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endauth


    <!-- Contact Information Card -->
    <div class="address-form-wrapper @if (auth('customer')->check() && $oldSessionAddressId !== 'new' && $isAvailableAddress) d-none @endif">

        <!-- Full Name Field -->
        <div class="form-group mb-4 @error('address.name') has-error @enderror">
            <label class="form-label fw-medium mb-2" for="address_name">
                Họ & Tên <span class="text-danger">*</span>
            </label>
            <input
                class="form-control form-control-lg"
                id="address_name"
                name="address[name]"
                autocomplete="name"
                type="text"
                value="{{ old('address.name', Arr::get($sessionCheckoutData, 'name')) ?: (auth('customer')->check() ? auth('customer')->user()->name : null) }}"
                placeholder="Nguyễn Văn A"
                required
            >
            {!! Form::error('address.name', $errors) !!}
        </div>

        <!-- Phone and Email Row -->
        <div class="row">
            @if (!in_array('phone', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div class="col-md-6 col-12">
                    <div class="form-group mb-4 @error('address.phone') has-error @enderror">
                        <label class="form-label fw-medium mb-2" for="address_phone">
                            Số điện thoại liên hệ <span class="text-danger">*</span>
                        </label>
                        <div class="phone-input-group">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    +84
                                </span>
                                <input
                                    class="form-control form-control-lg border-start-0"
                                    id="address_phone"
                                    name="address[phone]"
                                    autocomplete="phone"
                                    type="tel"
                                    value="{{ old('address.phone', Arr::get($sessionCheckoutData, 'phone')) ?: (auth('customer')->check() ? auth('customer')->user()->phone : null) }}"
                                    placeholder="Ví dụ: 096 969 3791"
                                    required
                                >
                            </div>
                        </div>
                        {!! Form::error('address.phone', $errors) !!}
                    </div>
                </div>
            @endif

            @if (!in_array('email', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div class="col-md-6 col-12">
                    <div class="form-group mb-4 @error('address.email') has-error @enderror">
                        <label class="form-label fw-medium mb-2" for="address_email">
                            Email
                        </label>
                        <input
                            class="form-control form-control-lg"
                            id="address_email"
                            name="address[email]"
                            autocomplete="email"
                            type="email"
                            value="{{ old('address.email', Arr::get($sessionCheckoutData, 'email')) ?: (auth('customer')->check() ? auth('customer')->user()->email : null) }}"
                            placeholder="Nguyễn Văn A"
                            required
                        >
                        {!! Form::error('address.email', $errors) !!}
                    </div>
                </div>
            @endif
        </div>

        {!! apply_filters('ecommerce_checkout_address_form_inside', null) !!}

        @if (EcommerceHelper::isUsingInMultipleCountries() && !in_array('country', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group mb-4 @error('address.country') has-error @enderror">
                <label class="form-label fw-medium mb-2" for="address_country">{{ __('Country') }}</label>
                <div class="select--arrow">
                    <select
                        class="form-control form-control-lg"
                        id="address_country"
                        name="address[country]"
                        autocomplete="country"
                        data-form-parent=".customer-address-payment-form"
                        data-type="country"
                        required
                    >
                        @foreach (EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                            <option
                                value="{{ $countryCode }}"
                                @if (old('address.country', Arr::get($sessionCheckoutData, 'country')) == $countryCode) selected @endif
                            >{{ $countryName }}</option>
                        @endforeach
                    </select>
                </div>
                {!! Form::error('address.country', $errors) !!}
            </div>
        @else
            <input
                id="address_country"
                name="address[country]"
                type="hidden"
                value="{{ EcommerceHelper::getFirstCountryId() }}"
            >
        @endif

        <div class="row">
            @if (!in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div class="col-sm-6 col-12">
                    <div class="form-group mb-4 @error('address.state') has-error @enderror">
                        <label class="form-label fw-medium mb-2" for="address_state">{{ __('State') }}</label>
                        @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                            <div class="select--arrow">
                                <select
                                    class="form-control form-control-lg"
                                    id="address_state"
                                    name="address[state]"
                                    autocomplete="state"
                                    data-form-parent=".customer-address-payment-form"
                                    data-type="state"
                                    data-url="{{ route('ajax.states-by-country') }}"
                                    required
                                >
                                    <option value="">{{ __('Select state...') }}</option>
                                    @if (old('address.country', Arr::get($sessionCheckoutData, 'country')) || !EcommerceHelper::isUsingInMultipleCountries())
                                        @foreach (EcommerceHelper::getAvailableStatesByCountry(old('address.country', Arr::get($sessionCheckoutData, 'country')) ?: EcommerceHelper::getFirstCountryId()) as $stateId => $stateName)
                                            <option
                                                value="{{ $stateId }}"
                                                @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) == $stateId) selected @endif
                                            >{{ $stateName }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-core::icon name="ti ti-chevron-down" />
                            </div>
                        @else
                            <input
                                class="form-control form-control-lg"
                                id="address_state"
                                name="address[state]"
                                autocomplete="state"
                                type="text"
                                value="{{ old('address.state', Arr::get($sessionCheckoutData, 'state')) }}"
                                required
                            >
                        @endif
                        {!! Form::error('address.state', $errors) !!}
                    </div>
                </div>
            @endif

            @if (!in_array('city', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div @class(['col-sm-6 col-12' => ! in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()), 'col-12' => in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout())])>
                    <div class="form-group mb-4 @error('address.city') has-error @enderror">
                        <label class="form-label fw-medium mb-2" for="address_city">{{ __('City') }}</label>
                        @if (EcommerceHelper::useCityFieldAsTextField())
                            <input
                                class="form-control form-control-lg"
                                id="address_city"
                                name="address[city]"
                                autocomplete="city"
                                type="text"
                                value="{{ old('address.city', Arr::get($sessionCheckoutData, 'city')) }}"
                                required
                            >
                        @else
                            <div class="select--arrow">
                                <select
                                    class="form-control form-control-lg"
                                    id="address_city"
                                    name="address[city]"
                                    autocomplete="city"
                                    data-type="city"
                                    data-using-select2="false"
                                    data-url="{{ route('ajax.cities-by-state') }}"
                                    required
                                >
                                    <option value="">{{ __('Select city...') }}</option>
                                    @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) || in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()))
                                        @foreach (EcommerceHelper::getAvailableCitiesByState(old('address.state', Arr::get($sessionCheckoutData, 'state')), old('address.country', Arr::get($sessionCheckoutData, 'country', EcommerceHelper::getFirstCountryId()))) as $cityId => $cityName)
                                            <option
                                                value="{{ $cityId }}"
                                                @if (old('address.city', Arr::get($sessionCheckoutData, 'city')) == $cityId) selected @endif
                                            >{{ $cityName }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-core::icon name="ti ti-chevron-down" />
                            </div>
                        @endif
                        {!! Form::error('address.city', $errors) !!}
                    </div>
                </div>
            @endif
        </div>

        @if (!in_array('address', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group @error('address.address') has-error @enderror">
                <label class="form-label fw-medium mb-2" for="address_address">{{ __('Address') }}</label>
                <input
                    class="form-control form-control-lg"
                    id="address_address"
                    name="address[address]"
                    autocomplete="address"
                    type="text"
                    value="{{ old('address.address', Arr::get($sessionCheckoutData, 'address')) }}"
                    required
                >
                {!! Form::error('address.address', $errors) !!}
            </div>
        @endif

        @if (EcommerceHelper::isZipCodeEnabled())
            <div class="form-group mb-4 @error('address.zip_code') has-error @enderror">
                <label class="form-label fw-medium mb-2" for="address_zip_code">{{ __('Zip code') }}</label>
                <input
                    class="form-control form-control-lg"
                    id="address_zip_code"
                    name="address[zip_code]"
                    autocomplete="postal-code"
                    type="text"
                    value="{{ old('address.zip_code', Arr::get($sessionCheckoutData, 'zip_code')) }}"
                    required
                >
                {!! Form::error('address.zip_code', $errors) !!}
            </div>
        @endif
    </div>


    @if (!auth('customer')->check())
        <div id="register-an-account-wrapper" class="bg-white rounded-3 p-4 shadow-sm border mt-4">
            <div class="mb-3 form-group">
                <div class="form-check">
                    <input
                        id="create_account"
                        name="create_account"
                        type="checkbox"
                        class="form-check-input"
                        value="1"
                        @if (old('create_account') == 1) checked @endif
                    >
                    <label
                        class="form-check-label fw-medium"
                        for="create_account"
                    >{{ __('Register an account with above information?') }}</label>
                </div>
            </div>

            <div class="password-group @if (!$errors->has('password') && !$errors->has('password_confirmation')) d-none @endif">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group mb-3 @error('password') has-error @enderror">
                            <label class="form-label fw-medium mb-2" for="password">{{ __('Password') }}</label>
                            <input
                                class="form-control form-control-lg"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="password"
                            >
                            {!! Form::error('password', $errors) !!}
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group mb-3 @error('password_confirmation') has-error @enderror">
                            <label class="form-label fw-medium mb-2" for="password-confirm">{{ __('Password confirmation') }}</label>
                            <input
                                class="form-control form-control-lg"
                                id="password-confirm"
                                name="password_confirmation"
                                type="password"
                                autocomplete="password-confirmation"
                            >
                            {!! Form::error('password_confirmation', $errors) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {!! apply_filters('ecommerce_checkout_address_form_after', null, $sessionCheckoutData) !!}
</div>

<style>
    .flex-1 {
        flex: 1;
    }

/* Invoice Section Styling */
.invoice-section-card {
    overflow: hidden;
    border: 1px solid #e9ecef !important;
}

.invoice-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}

.invoice-body {
   border-radius: 16px;
    background: #398CE5 !important;
    color: #fff;
}

.invoice-info-text span {
    font-size: 0.95rem;
    line-height: 1.5;
}


.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}


.form-label {
    color: #2c3e50;
    font-size: 0.95rem;
}

.form-control-lg {
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-control-lg:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control-lg::placeholder {
    color: #9ca3af;
    font-style: italic;
}

/* Phone Input Group Styling */
.phone-input-group .input-group-text {
    font-size: 0.9rem;
    color: #495057;
    font-weight: 500;
}

.phone-input-group .form-control {
    border-left: none;
}

.phone-input-group .form-control:focus {
    border-color: #007bff;
    box-shadow: none;
}

.phone-input-group .input-group-text:focus-within {
    border-color: #007bff;
}

/* Select Arrow Styling */
.select--arrow {
    position: relative;
}

.select--arrow select {
    appearance: none;
    background: white;
    padding-right: 2.5rem;
}

.select--arrow x-core\\:icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6c757d;
}

/* Error States */
.has-error .form-control-lg {
    border-color: #dc3545;
}

.has-error .form-control-lg:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .invoice-section-card,
    .address-form-wrapper {
        margin-bottom: 1rem;
    }

    .invoice-header {
        padding: 0.75rem 1rem !important;
    }

    .invoice-body {
        padding: 0.75rem 1rem !important;
    }

    .invoice-checkbox {
        padding: 0.75rem 1rem !important;
    }

    .form-control-lg {
        padding: 0.6rem 0.8rem;
        font-size: 0.95rem;
    }

    .phone-input-group .input-group-text {
        font-size: 0.85rem;
        padding: 0.6rem 0.8rem;
    }
}

@media (max-width: 576px) {
    .invoice-info-text span {
        font-size: 0.9rem;
    }

    .form-label {
        font-size: 0.9rem;
    }

    .form-control-lg {
        font-size: 0.9rem;
    }
}

/* Animation */
.invoice-section-card,
.address-form-wrapper {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 20px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}
</style>
