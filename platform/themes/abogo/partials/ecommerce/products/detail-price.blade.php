@if(!isset($data[$product_group->id]))
    <div class="d-flex justify-content-center">
        <div class="fs-16 fw-500">Hết phòng! Vui lòng chọn lại ngày khác hoặc chọn phòng khác! </div>
    </div>
@else
    <div class="d-flex justify-content-between">
        <div class="fs-16 fw-500">Số phòng</div>
        <div class="fs-16 fw-500">
            {{$data[$product_group->id]["number_room"]??1}}
        </div>
    </div>
    @foreach($data[$product_group->id]["prices"]??[] as $date=>$price)
        <div class="d-flex justify-content-between">
            <div class="fs-16 fw-500">Giá đêm {{Carbon\Carbon::parse($date)->format("d/m/Y")}}</div>
            <div class="fs-16 fw-500">
                {{ format_price($price) }}
            </div>
        </div>
    @endforeach
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
    <div class="d-flex justify-content-between">
        <div class="fs-16 fw-500">Tổng tiền</div>
        <div class="fs-16 fw-500 total-price"
             data-base-price="{{ $data[$product_group->id]["totalPrice"] }}">{{ format_price($data[$product_group->id]["totalPrice"]) }}</div>
    </div>

    <div>
        <button class="btn btn-main-color w-100" id="add_product_group" type="button">Thêm</button>
    </div>

    @if ($product->options()->count() > 0)
        {!! render_product_options($product) !!}
    @endif

@endif
