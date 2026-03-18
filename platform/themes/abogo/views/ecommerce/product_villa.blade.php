@php
    $policy = MetaBox::getMetaData($product, 'policy', true);
    $rule = MetaBox::getMetaData($product, 'rule', true);
    $beds = MetaBox::getMetaData($product, 'beds', true);
    $maxAdults = MetaBox::getMetaData($product, 'max_adults', true);
    $maxChildren = MetaBox::getMetaData($product, 'max_children', true);
    $size = MetaBox::getMetaData($product, 'size', true);
@endphp

<div class="product-service-detail container">
    <div class="feature-img">
        <img src="{{ RvMedia::getImageUrl($product->image, null, false, RvMedia::getDefaultImage()) }}"
             class="rounded-20" alt="product">
    </div>
    <h1 class="fw-600 fs-24 mt-2">{{ $product->name }}</h1>
    <div class="detail d-flex">
        <div class="detail-item me-3">
            <i class="fa-solid fa-bed me-2"></i>
            <span>{{ $beds }} giường</span>
        </div>
        <div class="detail-item me-3">
            <i class="fa-solid fa-people-group me-2"></i>
            <span>{{ $maxAdults }} Người lớn, {{ $maxChildren }} Trẻ em</span>
        </div>
        <div class="detail-item me-3">
            <i class="fa-solid fa-expand me-2"></i>
            <span>{{ $size }} m2</span>
        </div>
    </div>
    <div class="row pb-3 pb-md-0">
        <div class="col-12 col-md-8  pb-md-5">
            <div class="product-intro bg-white rounded-20 p-4 mt-3 h-100 text-center">
                <img src="{{ Theme::asset()->url('images/products/hotel-intro.png') }}" class="intro-img" alt="intro">
                <h3 class="fw-600 fs-20">{{ $product->name }}</h3>
                <div class="fs-16 fw-400 max-3-lines">{!! BaseHelper::clean($product->description) !!}
                </div>
                <div class="product-amenities d-flex flex-wrap justify-content-between p-4">
                    @foreach($product->extensions as $extension)
                        <div class="amenity-item col-md-3 justify-content-center d-flex align-items-center">
                            <div class="icon">
                                <img
                                    src="{{ RvMedia::getImageUrl($extension->image, null,false,RvMedia::getDefaultImage()) }}"
                                    alt="{{$extension->name}}">
                            </div>
                            <div class="name">{{$extension->name}}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4  pb-md-5">
            <div class="product-total d-flex flex-column bg-white rounded-20 p-4 mt-3 h-100 mobile-hidden">
                <div class="row mb-3">
                    <div class="col-4 px-0">
                        <img src="{{ RvMedia::getImageUrl($product->image, null, false, RvMedia::getDefaultImage()) }}"
                             alt="product" class="product-img rounded-10">
                    </div>
                    <div class="col-8">
                        <div class="fw-500 fs-16 max-3-lines">{{ $product->name }}</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500">Ngày nhận</div>
                    <div
                        class="fs-16 fw-500 date-start">{{ $startDate }}</div>
                </div>
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500">Ngày trả</div>
                    <div
                        class="fs-16 fw-500 date-end">{{ $endDate }}</div>
                </div>

{{--                <div class="d-flex justify-content-between my-1">--}}
{{--                    <div class="fs-16 fw-500">Số lượng</div>--}}
{{--                    <div class="fs-16 fw-500">--}}
{{--                        @if($product->with_storehouse_management == 1)--}}
{{--                            @if ($product->quantity > 0)--}}
{{--                                <input type="text" name="qty" id="qty_villa" min="0" value="1"--}}
{{--                                       max="{{$product->quantity}}">--}}
{{--                            @else--}}
{{--                                <span class="text-danger">Hết hàng</span>--}}
{{--                            @endif--}}
{{--                        @else--}}
{{--                            @if ($product->stock_status == "in_stock")--}}
{{--                                <input type="text" name="qty" id="qty_villa" min="0" value="1">--}}
{{--                            @else--}}
{{--                                <span class="text-danger">Hết hàng</span>--}}
{{--                            @endif--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500">Người lớn</div>
                    <div
                        class="fs-16 fw-500">{{$maxAdults}}</div>
                </div>
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500">Trẻ em</div>
                    <div
                        class="fs-16 fw-500">{{$maxChildren}}</div>
                </div>
                <div class="d-flex justify-content-between mt-auto mt-3">
                    <div class="fs-18 fw-700">Tổng tiền</div>
                    <div
                        class="fs-18 fw-700 total_price_product">{{ $price }}</div>
                </div>
                <div class="d-flex justify-content-center mt-3">

                    @if(isset($_GET["rowId"]))
                        <form
                            action="{{ route('public.cart.reschedule-villa',['product_id' => $product->id,"rowId" => $_GET["rowId"]]) }}"
                            method="post">
                            @csrf
                            <input type="text" hidden name="start_date" id="start_date" value="{{$startDate}}">
                            <input type="text" hidden name="end_date" id="end_date" value="{{$endDate}}">
                            <button class="btn btn-main-color rounded button-re-buy-now">Đặt ngay</button>
                        </form>
                    @else

                        <input type="text" hidden name="start_date" id="start_date" value="{{$startDate}}">
                        <input type="text" hidden name="end_date" id="end_date" value="{{$endDate}}">
                        @if($product->type=="hotel" || is_object($productsGroupedParent = $product->productsGroupedParent->first()))
                            <button class="btn btn-main-color rounded button-re-buy-now button-buy-now"
                                    data-id="{{ $product->id }}"
                                    data-origin_product_id="{{ $productsGroupedParent->id }}"
                                    data-url="{{ route('public.ajax.add-hotel-to-cart') }}">Đặt ngay
                            </button>
                        @elseif($product->type=="villa")
                            <button class="btn btn-main-color rounded button-re-buy-now button-buy-now"
                                    data-id="{{ $product->id }}"
                                    data-url="{{ route('public.ajax.add-villa-to-cart') }}">Đặt ngay
                            </button>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>


    <div class="row pt-3 pt-md-0">
        <div class="col-12 col-md-5">
            <div class="product-service-calendar d-flex flex-column h-100 bg-white rounded-20 p-3"
                 id="product-service-calendar" data-id="{{ $product->id }}"
                 data-url="{{ route('public.ajax.get.price.reschedule',['product_id' => $product->id,"rowId" => $_GET["rowId"]??""]) }}"
            ></div>
        </div>
        <div class="col-12 col-md-7">
            <div class="d-flex flex-wrap product-service-images hotel-gallery list">
                @foreach($productImages as $index => $image)
                    @if ($index < 4)
                        <div class="col-6 p-1 position-relative item">
                            <a href="{{ RvMedia::getImageUrl($image) }}" class="gallery-item d-block position-relative">
                                <img src="{{ RvMedia::getImageUrl($image) }}" alt="{{ $product->name }}"
                                     class="img-fluid rounded-10">
                                @if ($index == 3 && count($productImages) > 4)
                                    <div class="overlay">+{{ count($productImages) - 4 }}</div>
                                @endif
                            </a>
                        </div>
                    @else
                        <a href="{{ RvMedia::getImageUrl($image) }}" class="gallery-item d-none"></a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6 col-12 my-3">
            <div class="policies p-4 bg-white rounded-20">
                <div class="d-flex justify-content-between">
                    <h2 class="fw-600 fs-22">{{ __('Policies') }}</h2>
                    <a href="#">Xem thêm</a>
                </div>
                <div class="content">
                    {!! BaseHelper::clean($policy) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12 my-3">
            <div class="policies p-4 bg-white rounded-20">
                <div class="d-flex justify-content-between">
                    <h2 class="fw-600 fs-22">{{ __('Rules') }}</h2>
                    <a href="#">Xem thêm</a>
                </div>
                <div class="content">
                    {!! BaseHelper::clean($rule) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ajaxCalendarPriceRoute = "{{ route('public.ajax.product-villa-price-calendar',['product_id' => $product->id]) }}"
</script>
