@if(isset($data[$product_group->id]))
    <div class="product-total bg-white rounded-20 p-4 mobile-hidden">
        <div class="row mb-3">
            <div class="col-4 px-0">
                <img src="{{ RvMedia::getImageUrl($product_group->image) }}" alt="{{ $product_group->name }}"
                     class="product-img rounded-8">
            </div>
            <div class="col-8">
                <div class="fw-500 fs-16 max-3-lines">{{ $product_group->name }}
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="fs-16 fw-500">Giá {{count($data[$product_group->id]["prices"])}} đêm</div>
            <div class="fs-16 fw-500">
                {{ format_price(array_sum($data[$product_group->id]["prices"])) }}
            </div>
        </div>

        @if($data[$product_group->id]["children_surplus"]>0)
            <div class="d-flex justify-content-between">
                <div class="fs-16 fw-500">Phụ thu {{$data[$product_group->id]["children_surplus"]}} trẻ em</div>
                <div class="fs-16 fw-500">
                    {{ format_price($data[$product_group->id]["children_surplus_fee"]*$data[$product_group->id]["children_surplus"]) }}
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between">
            <div class="fs-16 fw-500">Phụ phí</div>
            <div class="fs-16 fw-500">{{ format_price($data[$product_group->id]["addon"]) }}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="fs-16 fw-500">Dịch vụ</div>
            <div class="fs-16 fw-500 price-service-fee">{{ format_price(0) }}</div>
        </div>


        <div class="d-flex justify-content-between mt-3">
            <div class="fs-18 fw-700">Tổng tiền</div>
            <div class="fs-18 fw-700 total-price" data-base-price="{{ $data[$product_group->id]["totalPrice"] }}">
                {{ format_price($data[$product_group->id]["totalPrice"]) }}
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            <button
                class="btn btn-main-color rounded button-buy-now @if ($product_group->isOutOfStock()) btn-disabled @endif"
                data-url="{{ route('public.ajax.add-hotel-to-cart') }}"
                data-id="{{ $product_group->id }}"
                data-origin_product_id="{{ $product->id }}"
            >
                Đặt ngay
            </button>
        </div>
    </div>

    <div class="mobile-menu product-page px-3 desktop-hidden">
        <div class="d-flex justify-content-between mb-2">
            <div class="product-name">
                {{ $product_group->name }}
            </div>
            <div class="product-price total-price" data-base-price="{{ $data[$product_group->id]["totalPrice"] }}">
                {{ format_price($data[$product_group->id]["totalPrice"]) }}
            </div>
        </div>

        <div class="d-flex justify-content-between">

            <span class="button-add-to-cart align-content-center"
                  data-url="{{ route('public.ajax.add-hotel-to-cart') }}"
                  data-id="{{ $product_group->id }}"
                  data-origin_product_id="{{ $product->id }}">
                <i class="fa fa-cart-plus" style="font-size: 24px!important;"></i>
            </span>
            <button
                type="button"
                class="btn btn-main-color w-100 rounded button-buy-now @if ($product_group->isOutOfStock()) btn-disabled @endif"
                data-url="{{ route('public.ajax.add-hotel-to-cart') }}"
                data-id="{{ $product_group->id }}"
                data-origin_product_id="{{ $product->id }}"
            >
                Đặt ngay
            </button>
        </div>
    </div>
@endif
