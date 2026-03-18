
<section data-bb-toggle="cart-content" class="cart-area py-4">
    <div class="container">

        <!-- Page Title -->
        <h2 class="fw-bold mb-4">Giỏ Hàng</h2>

        @if ($products->isNotEmpty())
            <div class="row">
                <div class="col-lg-8">
                    <!-- Cart Items -->
                    <x-core::form method="POST" :url="route('public.cart.update')" class="cart-form">
                        @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                            @php
                                $product = $products->find($cartItem->id);
                            @endphp

                            @continue(empty($product))

                            <div class="cart-item bg-white rounded-3 p-3 mb-3 shadow-sm border position-relative"
                                 data-bb-value="cart-row-{{ $cartItem->rowId }}">
                                <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">


                                <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-cart-item position-absolute clear-cart-item"
                                        data-url="{{ route('public.cart.remove', $cartItem->rowId) }}"
                                        data-row-id="{{ $cartItem->rowId }}"
                                        title="Xóa sản phẩm khỏi giỏ hàng">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="row align-items-start">
                                    <!-- Product Image -->
                                    <div class="col-md-2 col-12">
                                        <a href="{{ $product->original_product->url.'?rowId='.$cartItem->rowId }}">
                                            <img
                                                src="{{ RvMedia::getImageUrl($cartItem->options['image'], null, false, RvMedia::getDefaultImage()) }}"
                                                alt="{{ $product->original_product->name }}"
                                                class="img-fluid rounded-2"
                                                style="aspect-ratio: 1; object-fit: cover;">
                                        </a>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="col-md-10 col-12 pt-3 pt-md-0">
                                        <div class="cart-item-details">
                                            <!-- Product Name -->
                                            <h6 class="fw-bold mb-1">
                                                <a href="{{ $product->type =="tour"?$product->url:$product->url.'?rowId='.$cartItem->rowId }}"
                                                   class="text-decoration-none text-dark d-block pe-5">
                                                    {{ $product->original_product->name }}
                                                </a>
                                            </h6>

                                            @php
                                                $cartOptions = $cartItem->options;
                                                $days = $cartOptions->get('days', [$cartOptions->get('start_date')]);

                                                $attributes = $cartOptions->get('attributes', '');
                                                $adultsNumber = $cartOptions->get('adults_number', 0);
                                                $childrenNumber = $cartOptions->get('children_number', 0);
                                                $childrenSurplus = $cartOptions->get('children_surplus', 0);
                                                $priceChildrenSurplus = $cartOptions->get('priceChildrenSurplus', 0);
                                                $addOn = $cartOptions->get('addOn', 0);
                                                $services = $cartOptions->get('services', []);
                                            @endphp

                                                <!-- Basic Info Row -->
                                            <div class="basic-info">
                                                @if(!empty($days) && is_array($days) && count($days) > 0)
                                                    @php
                                                        $startDate = Carbon\Carbon::parse($days[0])->format('d');
                                                        $monthStart = Carbon\Carbon::parse($days[0])->format('m');
                                                        $endDate = Carbon\Carbon::parse(end($days))->addDay()->format('d');
                                                        $monthEnd = Carbon\Carbon::parse(end($days))->format('m');
                                                    @endphp

                                                    @if($product->type=="tour")
                                                        <small class="text-black d-block">
                                                            Ngày
                                                            đi: {{ Carbon\Carbon::parse($days[0])->format("d/m/Y") }}
                                                        </small>
                                                    @else
                                                        <small class="text-black d-block">
                                                            Ngày {{ $startDate }}/{{ $monthStart }} - {{ $endDate }}
                                                            /{{$monthEnd}}
                                                        </small>
                                                    @endif

                                                @endif

                                                <!-- <small class="text-black d-block">
                                                    Bao gồm ăn sáng theo số lượng người
                                                </small> -->

                                                <div class="guests-info mt-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        Người lớn: <span class="fw-medium">{{ $adultsNumber }}</span>
                                                        @if($childrenNumber > 0)
                                                            | Trẻ em: <span
                                                                class="fw-medium">{{ $childrenNumber }}</span>
                                                        @endif
                                                    </small>
                                                </div>

                                                @if($attributes)
                                                    <small class="text-muted d-block">{{ $attributes }}</small>
                                                @endif
                                            </div>

                                            <!-- Services Toggle -->
                                            @if(!empty($services) && is_array($services))
                                                <div class="services-toggle mt-2">
                                                    <button type="button"
                                                            class="btn btn-link btn-sm p-0 text-decoration-none fw-medium text-primary services-toggle-btn"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#services-{{ $cartItem->rowId }}"
                                                            aria-expanded="false">
                                                        <i class="fas fa-cog me-1"></i>
                                                        Lựa chọn sản phẩm đi kèm
                                                        <i class="fas fa-chevron-down ms-1 toggle-icon"></i>
                                                    </button>
                                                </div>

                                                <!-- Services Details (Collapsible) -->
                                                <div class="collapse services-info mt-2"
                                                     id="services-{{ $cartItem->rowId }}">
                                                    <div class="border-top pt-2">
                                                        @foreach($services as $service)
                                                            @if(is_array($service))
                                                                <div
                                                                    class="service-item d-flex justify-content-between align-items-center py-1 border-bottom"
                                                                    data-cart-id="{{ $cartItem->rowId }}"
                                                                    data-service-id="{{ $service['id'] }}">
                                                                    <div class="service-name small">
                                                                        <strong>{{ $service["name"] ?? '' }}</strong>
                                                                        <div class="text-muted"
                                                                             style="font-size: 0.75rem;">
                                                                            {{ format_price($service["price"] ?? 0) }}
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="service-controls d-flex align-items-center">
                                                                        <div
                                                                            class="service-quantity d-flex align-items-center me-2">
                                                                            <button type="button"
                                                                                    class="btn btn-outline-secondary btn-sm minus-service"
                                                                                    style="width: 25px; height: 25px; padding: 0; font-size: 0.75rem;">
                                                                                -
                                                                            </button>
                                                                            <input type="number"
                                                                                   disabled
                                                                                   class="form-control form-control-sm mx-1 text-center service-quantity-input"
                                                                                   value="{{ $service['quantity'] ?? 0 }}"
                                                                                   min="0"
                                                                                   max="99"
                                                                                   data-service-id="{{ $service['id'] }}"
                                                                                   data-price="{{ $service['price'] }}"
                                                                                   style="width: 40px; height: 25px; font-size: 0.75rem;">
                                                                            <button type="button"
                                                                                    class="btn btn-outline-secondary btn-sm plus-service"
                                                                                    style="width: 25px; height: 25px; padding: 0; font-size: 0.75rem;">
                                                                                +
                                                                            </button>
                                                                        </div>
                                                                        <div
                                                                            class="service-total text-primary small fw-bold"
                                                                            style="min-width: 80px; text-align: right;">
                                                                            {{ format_price(($service["price"] ?? 0) * ($service["quantity"] ?? 0)) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Price Info -->
                                            @php
                                                $priceServiceOther = $cartOptions->get('price_service_other', 0);
                                                $totalPrice = $cartItem->qty * $cartItem->price + (int)$addOn + (int)$priceChildrenSurplus + (int)$priceServiceOther;
                                            @endphp

                                            <div class="price-info mt-2">
                                                <div class="fw-bold text-primary">
                                                    Tổng:
                                                    <span class="total-price-item"
                                                          data-qty="{{$cartItem->qty}}"
                                                          data-price="{{$cartItem->price}}"
                                                          data-addon="{{(int)$addOn}}"
                                                          data-children-surplus="{{(int)$priceChildrenSurplus}}"
                                                    >{{ format_price($totalPrice) }}</span>
                                                </div>
                                                @if($product->type=="villa")
                                                    <small class="text-muted">Số căn: {{ $cartItem->qty }}</small>
                                                @elseif($product->type=="hotel")
                                                    <small class="text-muted">Số phòng: {{ $cartItem->qty }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-12">
                                        <div class="cart-actions text-end mt-3">
                                            <button type="button"
                                                    class="update-cart-item btn btn-primary btn-sm d-none rounded-pill px-4 py-2"
                                                    data-url="{{ route('public.ajax.cart.update-service') }}"
                                                    data-cart-id="{{ $cartItem->rowId }}">
                                                <i class="fas fa-sync-alt me-1"></i>
                                                <span>Cập nhật</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-core::form>

                    <!-- Coupon Section -->
                    <div class="coupon-section bg-white rounded-3 p-4 mb-3 shadow-sm">
                        <h6 class="fw-bold mb-3">Mã giảm giá</h6>
                        <x-core::form :url="route('public.coupon.apply')" method="post" data-bb-toggle="coupon-form"
                                      id="coupon-form">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="coupon_code"
                                           placeholder="Nhập mã giảm giá"
                                           value="{{ BaseHelper::stringify(old('coupon_code', session('applied_coupon_code'))) }}">
                                </div>
                                <div class="col-md-4">
                                    <button data-bb-toggle="coupon-form-btn" class="btn btn-outline-primary w-100"
                                            type="submit"
                                        @disabled(session(
                                'applied_coupon_code'))>Áp dụng
                                    </button>
                                </div>
                            </div>
                        </x-core::form>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary bg-white rounded-3 p-4 shadow-sm position-sticky" style="top: 100px;">
                        <h6 class="fw-bold mb-3">Tóm tắt đơn hàng</h6>

                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span class="amount_cart"
                                  data-bb-value="cart-subtotal">{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</span>
                        </div>

                        @if (EcommerceHelper::isTaxEnabled())
                            <div class="summary-row d-flex justify-content-between mb-2">
                                <span>Thuế:</span>
                                <span
                                    data-bb-value="cart-tax">{{ format_price(Cart::instance('cart')->rawTax()) }}</span>
                            </div>
                        @endif

                        @if ($couponDiscountAmount > 0 && session('applied_coupon_code'))
                            <div class="summary-row d-flex justify-content-between mb-2 text-success">
                                <span>Giảm giá ({{ session('applied_coupon_code') }}):</span>
                                <span
                                    data-bb-value="cart-coupon-discount-amount" class="coupon-discount-amount"
                                    data-price="{{$couponDiscountAmount}}">-{{ format_price($couponDiscountAmount) }}</span>
                            </div>
                        @endif

                        @if ($promotionDiscountAmount)
                            <div class="summary-row d-flex justify-content-between mb-2 text-success">
                                <span>Khuyến mãi:</span>
                                <span
                                    data-bb-value="cart-promotion-discount-amount" class="promotion-discount-amount"
                                    data-price="{{$promotionDiscountAmount}}">-{{ format_price($promotionDiscountAmount) }}</span>
                            </div>
                        @endif

                        <hr>

                        <div class="summary-total d-flex justify-content-between mb-3">
                            <span class="fw-bold">Tổng cộng:</span>
                            <span class="fw-bold text-primary fs-5 total_amount_carts" data-bb-value="cart-total">
                                {{ ($promotionDiscountAmount + $couponDiscountAmount) > Cart::instance('cart')->rawTotal() ? format_price(0) : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount) }}
                            </span>
                        </div>

                        {{--                        <small class="text-muted d-block mb-3">(Chưa bao gồm phí vận chuyển)</small>--}}

                        <!-- Checkout Button -->
                        <a href="{{ route('public.checkout.information', OrderHelper::getOrderSessionToken()) }}"
                           data-bb-toggle="cart-checkout"
                           class="btn btn-primary w-100 py-3 fw-bold mb-3">
                            Thanh Toán
                        </a>

                        <!-- Continue Shopping -->
                        <a href="{{ route('public.products') }}"
                           class="btn btn-outline-secondary w-100">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        @else
            @include(EcommerceHelper::viewPath('includes.empty-state'))
        @endif
    </div>
</section>


<style>
    .clear-cart-item {
        top: 15px;
        right: 15px;
        z-index: 10;
        border-radius: 50%;
        width: 30px;
        height: 30px;
    }

    .clear-cart-item:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .service-quantity-input::-webkit-outer-spin-button,
    .service-quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .service-quantity button {
        border: 1px solid #ccc !important;
        outline: none !important;
    }

    .service-quantity button:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    .cart-item {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef !important;
        border-radius: 20px !important;
    }

    .cart-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        border-color: #007bff !important;
    }

    .cart-actions .btn {
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .update-cart-item {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .update-cart-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    }

    .update-cart-item:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
    }

    .update-cart-item i {
        font-size: 0.8rem;
    }

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

    .service-item {
        border-bottom: 1px solid #f8f9fa !important;
        padding: 0.5rem 0;
    }

    .service-item:last-child {
        border-bottom: none !important;
    }

    .service-controls button {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-quantity-input {
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        background: #fff !important;
        border: none !important;
        padding: 0;
        -moz-appearance: textfield;
    }

    .service-quantity-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .price-info {
        font-size: 0.95rem;
    }

    .guests-info i {
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .cart-item {
            padding: 1rem !important;
        }

        .cart-actions {
            margin-top: 1rem;
        }

        .cart-actions .btn {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .service-controls {
            flex-direction: column;
            align-items: flex-end;
        }

        .service-quantity {
            margin-bottom: 0.25rem;
        }
    }

    @media (max-width: 576px) {
        .col-3 {
            flex: 0 0 auto;
            width: 30%;
        }

        .col-6 {
            flex: 0 0 auto;
            width: 45%;
        }

        .col-3:last-child {
            flex: 0 0 auto;
            width: 25%;
        }
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
