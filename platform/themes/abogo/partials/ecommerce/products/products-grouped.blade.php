@foreach($product->groupedProduct as $item)
    @if(isset($data[$item->id]))
        <div class="service-item d-flex align-items-center py-3">
            <img src="{{ RvMedia::getImageUrl($item->image, null,false,RvMedia::getDefaultImage()) }}"
                 alt="{{ $item->image }}" class="rounded-10">

            <div class="room-content flex-grow-1 px-3">
                <h5 class="fw-600 fs-18 max-2-lines">{{ $item->name }}</h5>
                <div class="info">
                    <div class="price-service-product price fw-600 fs-18 me-4">
                        {{format_price($data[$item->id]["totalPrice"]-$data[$item->id]["children_surplus_fee"] * $data[$item->id]["children_surplus"])}}
                    </div>
                    <div class="fw-500 fs-18 max-people">
                        {{ $data[$item->id]["number_room"] }} Phòng, Tối
                        đa {{ $data[$item->id]["maxAdults"] }} người
                        lớn, {{$data[$item->id]["maxChildren"]}} trẻ em
                    </div>
                </div>
            </div>

            <div class="text-end">
            <span class="button-add-to-cart @if ($item->isOutOfStock()) btn-disabled @endif"
                  data-url="{{ route('public.ajax.add-hotel-to-cart') }}"
                  data-id="{{ $item->id }}"
                  data-origin_product_id="{{ $product->id }}">
                <i class="fa-solid fa-circle-plus fs-22"></i>
            </span>
            </div>
        </div>
    @endif
@endforeach
