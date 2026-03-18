@dd(Html::script('vendor/core/plugins/ecommerce/js/checkout.js?v=3.4.0'))
@if (Cart::instance('cart')->isNotEmpty())
    <x-core::form :url="route('public.checkout.process', $token)" id="checkout-form"
                  class="checkout-form payment-checkout-form">
        <input id="checkout-token" name="checkout-token" type="hidden" value="{{ $token }}">

        <div class="container" id="main-checkout-product-info">

{{--            <div class="row align-items-center g-3">--}}
{{--                <div class="order-2 order-md-1 col-md-6 text-center text-md-start mb-4 mb-md-0">--}}
{{--                    <a class="text-info" href="{{ route('public.cart') }}">--}}
{{--                        <x-core::icon name="ti ti-arrow-narrow-left"/>--}}
{{--                        <span class="d-inline-block back-to-cart">{{ __('Back to cart') }}</span>--}}
{{--                    </a>--}}

{{--                    {!! apply_filters('ecommerce_checkout_form_after_back_to_cart_link', null, $products) !!}--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="row">
                <div class="order-0 order-md-1 col-lg-5 col-md-6 right">
                    <div class="position-relative" id="cart-item">
                        <div class="payment-info-loading" style="display: none;">
                            <div class="payment-info-loading-content">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        @if (count($products) > 0)
                            @foreach(Cart::instance('cart')->content() as $key => $item)
                                @php
                                    $product = $products->find($item->id);
                                @endphp
                                @if(!empty($product))
                                    @php
                                        $cartOptions = $item->options;
                                        $days = $cartOptions->get('days', []);
                                        $attributes = $cartOptions->get('attributes', '');
                                        $adultsNumber = $cartOptions->get('adults_number', 0);
                                        $childrenNumber = $cartOptions->get('children_number', 0);
                                        $childrenSurplus = $cartOptions->get('children_surplus', 0);
                                        $priceChildrenSurplus = $cartOptions->get('priceChildrenSurplus', 0);
                                        $addOn = $cartOptions->get('addOn', 0);
                                        $services = $cartOptions->get('services', []);
                                        $priceServiceOther = $cartOptions->get('price_service_other', 0);
                                        $totalPrice = $item->qty * $item->price + (int)$addOn + (int)$priceChildrenSurplus + (int)$priceServiceOther;
                                    @endphp

                                    <div class="checkout-cart-item bg-white rounded-3 p-3 mb-3 shadow-sm border">
                                        <div class="row align-items-start">
                                            <!-- Product Image -->
                                            <div class="col-md-2 col-12">
                                                <div class="checkout-product-img-wrapper position-relative">
                                                    <img class="img-fluid rounded-2"
                                                         src="{{ RvMedia::getImageUrl($item->options->image, null,false,RvMedia::getDefaultImage()) }}"
                                                         alt="{{ $product->name }}"
                                                         style="aspect-ratio: 1; object-fit: cover;">
                                                    <span class="checkout-quantity position-absolute top-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                          style="width: 24px; height: 24px; font-size: 0.75rem; margin: 5px;">{{ $item->qty }}</span>
                                                </div>
                                            </div>

                                            <!-- Product Details -->
                                            <div class="col-md-10 col-12 pt-3 pt-md-0">
                                                <div class="checkout-item-details">
                                                    <!-- Product Name -->
                                                    <h6 class="fw-bold mb-1">
                                                        {{ $product->name }}
                                                    </h6>

                                                    <!-- Basic Info Row -->
                                                    <div class="basic-info">
                                                        @if(!empty($days) && is_array($days) && count($days) > 0)
                                                            @php
                                                                $startDate = Carbon\Carbon::parse($days[0])->format('d');
                                                                $endDate = Carbon\Carbon::parse(end($days))->addDay()->format('d');
                                                                $monthStart = Carbon\Carbon::parse($days[0])->format('m');
                                                                $monthEnd = Carbon\Carbon::parse(end($days))->format('m');
                                                            @endphp
                                                            <small class="text-black d-block">
                                                                Ngày {{ $startDate }}/{{ $monthStart }} - Ngày {{ $endDate }}/{{$monthEnd}}
                                                            </small>
                                                        @endif

                                                        <div class="guests-info mt-1">
                                                            <small class="text-muted">
                                                                <i class="fas fa-users me-1"></i>
                                                                Người lớn: <span class="fw-medium">{{ $adultsNumber }}</span>
                                                                @if($childrenNumber > 0)
                                                                    | Trẻ em: <span class="fw-medium">{{ $childrenNumber }}</span>
                                                                @endif
                                                            </small>
                                                        </div>

                                                        @if($attributes)
                                                            <small class="text-muted d-block">{{ $attributes }}</small>
                                                        @endif
                                                    </div>

                                                                                        <!-- Services Info -->
                                    @if(!empty($services) && is_array($services))
                                        <div class="services-toggle mt-2">
                                            <button type="button"
                                                    class="btn btn-link btn-sm p-0 text-decoration-none fw-medium text-primary services-toggle-btn"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#checkout-services-{{ $key }}"
                                                    aria-expanded="true">
                                                <i class="fas fa-cog me-1"></i>
                                                Dịch vụ đi kèm
                                                <i class="fas fa-chevron-down ms-1 toggle-icon"></i>
                                            </button>
                                        </div>

                                        <!-- Services Details (Collapsible) -->
                                        <div class="collapse show services-info mt-2" id="checkout-services-{{ $key }}">
                                            <div class="">
                                                @foreach($services as $service)
                                                    @if(is_array($service) && ($service['quantity'] ?? 0) > 0)
                                                        <div class="service-item d-flex justify-content-between align-items-center py-1">
                                                            <div class="service-name small">
                                                                <strong>{{ $service["name"] ?? '' }}</strong>
                                                                <span class="text-muted">(x{{ $service['quantity'] ?? 0 }})</span>
                                                            </div>
                                                            <div class="service-total text-primary small fw-bold">
                                                                {{ format_price(($service["price"] ?? 0) * ($service["quantity"] ?? 0)) }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                                    <!-- Price Info -->
                                                    <div class="price-info pt-3">
                                                        <!-- Base Price -->
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <small class="text-muted">Giá phòng ({{ $item->qty }} ngày):</small>
                                                            <small class="fw-medium">{{ format_price($item->qty * $item->price) }}</small>
                                                        </div>

                                                        <!-- Additional Charges -->
                                                        @if($childrenSurplus > 0)
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted">Phụ thu ({{$childrenSurplus}} trẻ em):</small>
                                                                <small class="fw-medium">{{ format_price($priceChildrenSurplus) }}</small>
                                                            </div>
                                                        @endif

                                                        @if($addOn > 0)
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted">Phí dịch vụ:</small>
                                                                <small class="fw-medium">{{ format_price($addOn) }}</small>
                                                            </div>
                                                        @endif

                                                        @if($priceServiceOther > 0)
                                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                                <span class="fw-bold text-primary">
                                                                    Tổng dịch vụ đi kèm:
                                                                </span>
                                                                <span class="fw-bold text-primary">{{ format_price($priceServiceOther) }}</span>
                                                            </div>
                                                        @endif

                                                        <!-- Total Price -->
                                                        <hr class="my-2 bg-secondary">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold fs-6">Tổng cộng:</span>
                                                            <span class="fw-bold text-success fs-5">{{ format_price($totalPrice) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                    </div>

                    <!-- Order Summary Card -->
                    <div class="card border-0 shadow-sm mt-3 order-summary-card">
                        <div class="card-body p-0">
                            <!-- Subtotal -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <span class="text-muted">{{ __('Subtotal') }}:</span>
                                <span class="fw-medium price-text sub-total-text">
                                    {{ format_price(Cart::instance('cart')->rawSubTotal()) }}
                                </span>
                            </div>

                            <!-- Tax -->
                            @if (EcommerceHelper::isTaxEnabled())
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <span class="text-muted">
                                        {{ __('Tax') }}
                                        @if (Cart::instance('cart')->rawTax())
                                            <small class="text-muted">({{ Cart::instance('cart')->taxClassesName() }})</small>
                                        @endif
                                    </span>
                                    <span class="fw-medium price-text tax-price-text">
                                        {{ format_price(Cart::instance('cart')->rawTax()) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Coupon Code -->
                            @if (session('applied_coupon_code'))
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                                    <span class="text-success fw-medium">
                                        <i class="fas fa-ticket-alt me-1"></i>
                                        {{ __('Coupon code') }}:
                                    </span>
                                    <span class="text-success fw-bold price-text coupon-code-text">
                                        {{ session('applied_coupon_code') }}
                                    </span>
                                </div>
                            @endif

                            <!-- Coupon Discount -->
                            @if ($couponDiscountAmount > 0)
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <span class="text-success">{{ __('Coupon code discount amount') }}:</span>
                                    <span class="text-success fw-bold price-text total-discount-amount-text">
                                        -{{ format_price($couponDiscountAmount) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Promotion Discount -->
                            @if ($promotionDiscountAmount > 0)
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <span class="text-success">{{ __('Promotion discount amount') }}:</span>
                                    <span class="text-success fw-bold price-text">
                                        -{{ format_price($promotionDiscountAmount) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Shipping Fee -->
                            @if (!empty($shipping) && Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <span class="text-muted">
                                        <i class="fas fa-shipping-fast me-1"></i>
                                        {{ __('Shipping fee') }}:
                                    </span>
                                    <span class="fw-medium price-text shipping-price-text">
                                        {{ format_price($shippingAmount) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Total -->
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light">
                                <span class="fw-bold fs-6 text-dark">
                                    <i class="fas fa-calculator me-2"></i>
                                    {{ __('Total') }}:
                                </span>
                                <span class="fw-bold fs-6 text-primary total-text raw-total-text"
                                        data-price="{{ format_price($rawTotal, null, true) }}">
                                    {{ format_price($orderAmount) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3 mb-5">
                        @include(EcommerceHelper::viewPath('discounts.partials.form'), compact('discounts'))
                    </div>
                </div>

                <div class="col-lg-7 col-md-6 left">

                    <div class="form-checkout">
                        {!! apply_filters('ecommerce_checkout_form_before', null, $products) !!}

                        <div class="card bg-white rounded-3 mb-4 shadow-sm border p-4">
                            @if ($isShowAddressForm)
                                <div>
                                    <h5 class="checkout-payment-title fw-bold mb-0 mb-3">{{ __('Shipping information') }}</h5>
                                    <input
                                        id="save-shipping-information-url"
                                        type="hidden"
                                        value="{{ route('public.checkout.save-information', $token) }}"
                                    >
                                    @include(
                                        'plugins/ecommerce::orders.partials.address-form',
                                        compact('sessionCheckoutData')
                                    )
                                </div>

                                {!! apply_filters('ecommerce_checkout_form_after_shipping_address_form', null, $products) !!}
                            @endif
                        </div>


                        <!-- Invoice Section -->
                        <div class="card bg-white rounded-3 mb-4 shadow-sm border mt-4 p-4">
                            <h5 class="fw-bold mb-0 mb-3">
                                Xuất hoá đơn
                            </h5>
                            <div class="invoice-body bg-primary rounded-4 p-3">
                                <div class="invoice-info-text">
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" class="mt-1">
                                            <g clip-path="url(#clip0_3002_1325)">
                                                <path d="M10.5333 1.96016C10.9066 1.58683 10.9066 0.986829 10.5333 0.613496C10.1599 0.240163 9.55994 0.240163 9.1866 0.613496L5.09994 4.70016L3.39327 2.9935C3.01994 2.62016 2.41993 2.62016 2.0466 2.9935C1.67327 3.36683 1.67327 3.96683 2.0466 4.34016L4.4266 6.72016C4.79994 7.0935 5.39993 7.0935 5.77327 6.72016L10.5333 1.96016Z" fill="white"/>
                                                <path d="M13.3865 5.7668C13.7598 5.39347 13.7598 4.79347 13.3865 4.42014C13.0132 4.0468 12.4132 4.0468 12.0398 4.42014L5.09984 11.3668L1.96651 8.23347C1.59318 7.86014 0.993177 7.86014 0.619844 8.23347C0.24651 8.6068 0.24651 9.2068 0.619844 9.58014L4.42651 13.3868C4.79984 13.7601 5.39984 13.7601 5.77318 13.3868L13.3932 5.7668H13.3865Z" fill="white"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3002_1325">
                                                    <rect width="13.3333" height="13.3333" fill="white" transform="translate(0.333008 0.333496)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <span class="text-white flex-1">Bạn muốn xuất hoá đơn, để Abogo hỗ trợ Tiết kiệm thời gian, xuất hoá đơn nhanh chóng, đúng luật pháp thời gian cho kỳ nghỉ của bạn, hãy tận hưởng</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" class="mt-1">
                                            <g clip-path="url(#clip0_3002_1325)">
                                                <path d="M10.5333 1.96016C10.9066 1.58683 10.9066 0.986829 10.5333 0.613496C10.1599 0.240163 9.55994 0.240163 9.1866 0.613496L5.09994 4.70016L3.39327 2.9935C3.01994 2.62016 2.41993 2.62016 2.0466 2.9935C1.67327 3.36683 1.67327 3.96683 2.0466 4.34016L4.4266 6.72016C4.79994 7.0935 5.39993 7.0935 5.77327 6.72016L10.5333 1.96016Z" fill="white"/>
                                                <path d="M13.3865 5.7668C13.7598 5.39347 13.7598 4.79347 13.3865 4.42014C13.0132 4.0468 12.4132 4.0468 12.0398 4.42014L5.09984 11.3668L1.96651 8.23347C1.59318 7.86014 0.993177 7.86014 0.619844 8.23347C0.24651 8.6068 0.24651 9.2068 0.619844 9.58014L4.42651 13.3868C4.79984 13.7601 5.39984 13.7601 5.77318 13.3868L13.3932 5.7668H13.3865Z" fill="white"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3002_1325">
                                                    <rect width="13.3333" height="13.3333" fill="white" transform="translate(0.333008 0.333496)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <span class="text-white flex-1">Abogo đảm bảo xuất hoá đơn.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">Khi cần xuất hoá đơn GTGT, Quý khách vui lòng gửi yêu cầu đến Abogo kể từ thời điểm nhận mail đến trước 15h00 ngày hôm sau</div>

                            <div class="invoice-checkbox mt-3 bg-white">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="request_invoice"
                                        name="request_invoice"
                                        value="1"
                                        @if (old('request_invoice')) checked @endif
                                    >
                                    <label class="form-check-label fw-medium" for="request_invoice">
                                        Tôi muốn xuất hoá đơn
                                    </label>
                                </div>
                            </div>
                        </div>

                        {!! apply_filters('ecommerce_checkout_form_before_payment_form', null, $products) !!}

                        <input
                            name="amount"
                            type="hidden"
                            value="{{ format_price($orderAmount, null, true) }}"
                        >

                        @if (is_plugin_active('payment') && $orderAmount)
                            @php
                                $paymentMethods = apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                        'amount' => format_price($orderAmount, null, true),
                                        'currency' => strtoupper(get_application_currency()->title),
                                        'name' => null,
                                        'selected' => PaymentMethods::getSelectedMethod(),
                                        'default' => PaymentMethods::getDefaultMethod(),
                                        'selecting' => PaymentMethods::getSelectingMethod(),
                                    ]) . PaymentMethods::render();
                            @endphp

                            <input
                                name="currency"
                                type="hidden"
                                value="{{ strtoupper(get_application_currency()->title) }}"
                            >

                            @if($paymentMethods)
                                <div class="position-relative mb-4">
                                    <div class="payment-info-loading" style="display: none;">
                                        <div class="payment-info-loading-content">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                    </div>

                                    {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}

                                    <div class="card bg-white rounded-3 mb-4 shadow-sm border mt-4 p-4">
                                        <h5 class="checkout-payment-title fw-bold mb-0 mb-3">{{ __('Payment method') }}</h5>
                                        <ul class="list-group list_payment_method">
                                            {!! $paymentMethods !!}
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {!! apply_filters('ecommerce_checkout_form_after_payment_form', null, $products) !!}


                        <div class="card bg-white rounded-3 mb-4 shadow-sm border mt-4 p-4">
                            <div @class([
                                'form-group mb-3', 'has-error' => $errors->has('description')])>
                                <h5 class="checkout-payment-title fw-bold mb-0 mb-3" for="description">{{ __('Order notes') }}</h5>
                                <textarea
                                    class="form-control"
                                    id="description"
                                    name="description"
                                    rows="3"
                                    placeholder="{{ __('Notes about your order, e.g. special notes for delivery.') }}"
                                >{{ old('description') }}</textarea>
                                {!! Form::error('description', $errors) !!}
                            </div>
                        </div>

                        @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal())
                            <div role="alert" class="alert alert-warning">
                                {{ __('Minimum order amount is :amount, you need to buy more :more to place an order!', ['amount' => format_price(EcommerceHelper::getMinimumOrderAmount()), 'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal())]) }}
                            </div>
                        @endif

                        @if (EcommerceHelper::isDisplayTaxFieldsAtCheckoutPage())
                            @include(
                                'plugins/ecommerce::orders.partials.tax-information',
                                compact('sessionCheckoutData')
                            )

                            {!! apply_filters('ecommerce_checkout_form_after_tax_information_form', null, $products) !!}
                        @endif

                        @if($privacyPolicyUrl = theme_option('ecommerce_term_and_privacy_policy_url'))
                            <div class="form-check ps-0 mb-3">
                                <input
                                    id="agree_terms_and_policy"
                                    name="agree_terms_and_policy"
                                    type="checkbox"
                                    value="1"
                                    @checked (old('agree_terms_and_policy', true))
                                >
                                <label class="form-check-label" for="agree_terms_and_policy">
                                    {!! BaseHelper::clean(__(
                                        'I agree to the :link',
                                        ['link' => Html::link($privacyPolicyUrl, __('Terms and Privacy Policy'), attributes: ['class' => 'text-decoration-underline', 'target' => '_blank'])]
                                    )) !!}
                                </label>
                            </div>
                        @endif

                        {!! apply_filters('ecommerce_checkout_form_after', null, $products) !!}


                    </div>

                    <!-- Order Summary Card -->
                    <div class="card border-0 shadow-sm mt-3 order-summary-card">
                        <div class="card-body p-0">
                            <!-- Checkout Button -->
                            <div class="p-3">
                                @if (EcommerceHelper::isValidToProcessCheckout())
                                    <button
                                        class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3 checkout-summary-btn"
                                        type="submit"
                                        form="checkout-form"
                                        data-processing-text="{{ __('Processing. Please wait...') }}"
                                        data-error-header="{{ __('Error') }}"
                                    >
                                        <i class="fas fa-credit-card me-2"></i>
                                        {{ __('Checkout') }}
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-lg w-100 py-3 fw-bold rounded-3" disabled>
                                        <i class="fas fa-lock me-2"></i>
                                        {{ __('Checkout') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-core::form>
@else
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning my-5">
                    <span>{!! __('No products in cart. :link!', ['link' => Html::link(BaseHelper::getHomepageUrl(), __('Back to shopping'))]) !!}</span>
                </div>
            </div>
        </div>
    </div>
@endif

@push('footer')
    <script type="text/javascript" src="{{ asset('vendor/core/core/js-validation/js/js-validation.js') }}"></script>

    {!! JsValidator::formRequest(
        Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest::class,
        '#checkout-form',
    ) !!}

@endpush

<style>
    .list_payment_method li {
        border: none;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .list_payment_method {
        border: none !important;
    }
    .checkout-cart-item {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef !important;
        border-radius: 15px !important;
    }

    .checkout-cart-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        border-color: #007bff !important;
    }

    .checkout-product-img-wrapper {
        position: relative;
    }

    .checkout-quantity {
        font-weight: 600;
    }

    .checkout-item-details h6 {
        color: #2c3e50;
        font-size: 1rem;
        line-height: 1.4;
    }

    .basic-info small {
        line-height: 1.5;
    }

    .guests-info i {
        color: #6c757d;
    }

    .services-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.75rem;
    }

    .service-item {
        border-bottom: 1px solid #e9ecef;
        padding: 0.5rem 0;
    }

    .service-item:last-child {
        border-bottom: none;
    }

    .service-name strong {
        color: #495057;
    }

    .price-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 0.75rem;
    }

    /* Price breakdown styling */
    .price-info .d-flex {
        font-size: 0.95rem;
    }

    .price-info .text-muted {
        color: #6c757d !important;
        font-weight: 500;
    }

    .price-info .fw-medium {
        color: #495057;
        font-weight: 600;
    }

    /* Services total highlight */
    .price-info .bg-light {
        background-color: #e7f3ff !important;
    }

    .price-info .text-primary {
        color: #0066cc !important;
    }

    /* Final total styling */
    .price-info hr {
        border-color: #dee2e6;
        opacity: 1;
    }

    .price-info .text-success {
        color: #198754 !important;
        font-size: 1.2rem;
    }


    .price-info .fs-5 {
        font-size: 1.25rem !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .checkout-cart-item {
            padding: 1rem !important;
        }

        .checkout-item-details {
            padding-left: 0;
            margin-top: 1rem;
        }

        .price-info {
            padding: 0.75rem;
        }

        .price-info .d-flex {
            font-size: 0.9rem;
        }

        .price-info .text-success {
            font-size: 1.1rem !important;
        }

        .price-info .fs-5 {
            font-size: 1.15rem !important;
        }
    }

    @media (max-width: 576px) {
        .checkout-product-img-wrapper{
            max-width: 100px;
            margin: 0 auto;
        }
        .checkout-item-details h6 {
            font-size: 0.9rem;
        }

        .basic-info small,
        .guests-info small {
            font-size: 0.75rem;
        }
    }

    /* Enhanced visual elements */
    .checkout-cart-item .border-top {
        border-color: #dee2e6 !important;
    }

    .service-total {
        min-width: 80px;
    }

    .checkout-quantity {
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
    }

    /* Loading states */
    .payment-info-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 10;
        border-radius: 15px;
    }

    .payment-info-loading-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        color: #007bff;
    }

    /* Order Summary Card Styling */
    .order-summary-card {
        border-radius: 15px !important;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
    }

    .order-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12) !important;
    }

    .order-summary-card .card-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }

    .order-summary-card .card-header h6 {
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }

    .order-summary-card .card-body {
        background: #ffffff;
    }

    .order-summary-card .d-flex {
        transition: background-color 0.2s ease;
    }

    .order-summary-card .d-flex:hover {
        background-color: #f8f9fa !important;
    }

    .order-summary-card .border-bottom {
        border-color: #e9ecef !important;
        border-width: 1px;
    }

    .order-summary-card .text-muted {
        color: #6c757d !important;
        font-weight: 500;
    }

    .order-summary-card .fw-medium {
        font-weight: 600 !important;
        color: #495057;
    }

    .order-summary-card .text-success {
        color: #198754 !important;
    }

    .order-summary-card .bg-light {
        background-color: #f0f8f0 !important;
    }

    .order-summary-card .text-primary {
        color: #007bff !important;
    }

    /* Total section styling */
    .order-summary-card .bg-light:last-of-type {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }

    .order-summary-card .fs-5 {
        font-size: 1.25rem !important;
    }

    .order-summary-card .fs-4 {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
    }

    /* Checkout button styling */
    .checkout-summary-btn {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        border: none !important;
        border-radius: 12px !important;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        position: relative;
        overflow: hidden;
    }

    .checkout-summary-btn:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }

    .checkout-summary-btn:active {
        transform: translateY(0);
    }

    .checkout-summary-btn .badge {
        background-color: rgba(255, 255, 255, 0.9) !important;
        color: #007bff !important;
        font-weight: 600;
        border-radius: 20px;
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    .checkout-summary-btn i {
        font-size: 1.1rem;
    }

    /* Icon styling */
    .order-summary-card .fas {
        width: 16px;
        text-align: center;
    }

    /* Responsive adjustments for order summary */
    @media (max-width: 768px) {
        .order-summary-card {
            margin-top: 1rem !important;
            border-radius: 12px !important;
        }

        .order-summary-card .card-header {
            padding: 0.75rem 1rem;
        }

        .order-summary-card .card-header h6 {
            font-size: 1rem;
        }

        .order-summary-card .d-flex {
            padding: 0.75rem 1rem !important;
            font-size: 0.9rem;
        }

        .order-summary-card .fs-5 {
            font-size: 1.1rem !important;
        }

        .order-summary-card .fs-4 {
            font-size: 1.3rem !important;
        }

        .checkout-summary-btn {
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem;
        }

        .checkout-summary-btn .badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }
    }

    @media (max-width: 576px) {
        .order-summary-card .d-flex {
            font-size: 0.85rem;
            padding: 0.6rem 0.75rem !important;
        }

        .order-summary-card .fs-4 {
            font-size: 1.2rem !important;
        }

        .checkout-summary-btn {
            font-size: 0.9rem;
            padding: 0.6rem 0.8rem !important;
        }
    }

    /* Animation for card appearance */
    .order-summary-card {
        animation: slideInUp 0.6s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 30px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    /* Services Toggle Styling */
    .services-toggle-btn {
        font-size: 0.875rem;
        border: none !important;
        box-shadow: none !important;
    }

    .services-toggle-btn:focus {
        outline: none;
        box-shadow: none;
    }

    .toggle-icon {
        transition: transform 0.3s ease;
        font-size: 0.75rem;
    }

    .services-toggle-btn[aria-expanded="true"] .toggle-icon {
        transform: rotate(180deg);
    }

    .services-info .service-item {
        border-bottom: 1px solid #f8f9fa !important;
        padding: 0.5rem 0;
    }

    .services-info .service-item:last-child {
        border-bottom: none !important;
    }
</style>

<script>
    @if (Session::has('success_msg'))
    const successMessage = '{{ session('success_msg') }}';
    @endif
    @if (Session::has('error_msg'))
    const errorMessage = '{{ session('error_msg') }}';
    @endif

</script>
