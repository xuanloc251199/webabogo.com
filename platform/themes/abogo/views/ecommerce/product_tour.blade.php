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
                    <div class="fs-16 fw-500">Ngày đi</div>
                    <div
                        class="fs-16 fw-500 date-start" id="selected-tour-date">
                        @if(count($tourDates) > 0)
                            {{ \Carbon\Carbon::parse(reset($tourDates)['start'])->format('d/m/Y') }}
                        @else
                            Chưa chọn
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500">Người lớn</div>
                    <div
                        class="fs-16 fw-500 adults-price">
                        {{$price_adults}}
                    </div>
                </div>
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500" data-price="0">Trẻ em</div>
                    <div
                        class="fs-16 fw-500 children-price">
                        {{$price_children}}
                    </div>
                </div>
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500 total-adults">Người lớn</div>
                    <div
                        class="fs-16 fw-500 counter-tour">
                        <div class="counter">
                            <button class="btn minus">-</button>
                            <span class="count">{{$adults_number}}</span>

                            <button class="btn plus">+</button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between my-1">
                    <div class="fs-16 fw-500 total-children">Trẻ em</div>
                    <div
                        class="fs-16 fw-500 counter-tour">
                        <div class="counter">
                            <button class="btn minus">-</button>
                            <span class="count">{{$children_number}}</span>

                            <button class="btn plus">+</button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-auto mt-3">
                    <div class="fs-18 fw-700">Tổng tiền</div>

                    <div id="selected-tour-price" class="fs-18 fw-700 total_price_product_tour">
                        {!!  $price   !!}
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-3">
                        <input type="number" hidden name="qty" value="1">
                        <input type="date" hidden name="start_date" id="start_date" value="{{$startDate}}">
                        <input type="number" data-price="{{$price_adults}}" name="adults_number" id="adults_number"
                               value="{{$adults_number}}"
                               hidden="">
                        <input type="number" name="children_number" data-price="{{$price_children}}"
                               id="children_number"
                               value="{{$children_number}}"
                               hidden="">
                        <input type="number" name="adult_variation_id" id="adult_variation_id"
                               hidden="">
                        <input type="number" name="children_variation_id" id="children_variation_id"
                               hidden="">
                        <button class="btn btn-main-color rounded button-add-tour-to-cart"
                                data-id="{{ $product->id }}"
                                data-url="{{ route('public.ajax.add-tour-to-cart') }}"
                        >Đặt ngay
                        </button>

                </div>
            </div>
        </div>
    </div>


    <div class="row pt-3 pt-md-0">
        <div class="col-12 col-md-5">
            <div class="product-tour-calendar d-flex flex-column h-100 bg-white rounded-20 p-3"
                 id="product-tour-calendar"
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
    const dataPriceTour = {!! json_encode($tourDates) !!};
</script>
