@php
    use Illuminate\Support\Str;
    $showAvgRating ??= $product->reviews->isNotEmpty();
    $avgRating = number_format($product->reviews_avg ?: 0, 2);
@endphp

<style>
    span.count_service {
        min-width: 24px;
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    /* Product Search Styling */
    .product-search {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    /* Date Range Picker Styles */
    .date-range-picker {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 220px;
    }

    .date-range-picker input[type="text"] {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 8px 40px 8px 12px;
        font-size: 14px;
        color: #333;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        min-width: 200px;
    }

    .date-range-picker input[type="text"]:hover {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
    }

    .date-range-picker input[type="text"]:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }

    .date-range-picker i {
        position: absolute;
        right: 12px;
        color: #666;
        font-size: 16px;
        pointer-events: none;
        z-index: 1;
    }

    /* Guest Picker Styles */
    .guest-picker {
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: white;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 200px;
    }

    .guest-picker:hover {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
    }

    .guest-picker[data-popover-active="true"] {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }

    .guest-picker .detail {
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

    .guest-picker i {
        color: #666;
        font-size: 16px;
    }

    /* Room Type Picker Styles */
    .room-type-picker {
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: white;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 180px;
    }

    .room-type-picker:hover {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }

    .room-type-picker[data-popover-active="true"] {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        transform: translateY(-1px);
    }

    .room-type-picker .room-type-detail {
        font-size: 14px;
        color: #333;
        font-weight: 500;
        flex: 1;
    }

    .room-type-picker i.fa-bed {
        color: #666;
        font-size: 14px;
    }

    .room-type-picker i.fa-chevron-down {
        color: #999;
        font-size: 12px;
        transition: transform 0.2s ease;
    }

    .room-type-picker[data-popover-active="true"] i.fa-chevron-down {
        transform: rotate(180deg);
    }

    /* Popover Styles */
    .custom-popover {
        display: none;
        position: absolute;
        z-index: 1000;
        min-width: 300px;
        max-width: 450px;
    }

    .filter-popover {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px 16px;
        border-bottom: 1px solid #e5e5e5;
    }

    .filter-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .btn-close:hover {
        background-color: #f5f5f5;
        color: #666;
    }

    .filter-body {
        padding: 20px 24px;
        max-height: 400px;
        overflow-y: auto;
    }

    .filter-section {
        margin-bottom: 0;
    }

    /* Guest Options */
    .guest-options {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .guest-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0;
    }

    .guest-info {
        display: flex;
        flex-direction: column;
    }

    .guest-label {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 2px;
    }

    .guest-sublabel {
        font-size: 13px;
        color: #666;
    }

    .guest-counter {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .counter-btn {
        width: 32px;
        height: 32px;
        border: 1px solid #ddd;
        border-radius: 50%;
        background: white;
        color: #333;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
    }

    .product-total {
        position: sticky;
        top: 0;
    }

    .service-quantity .counter-btn {
        font-size: unset;
        font-weight: unset;
    }

    .counter-btn:hover {
        border-color: #6366f1;
        background-color: #6366f1;
        color: white;
        transform: none;
    }

    .counter-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: white;
        transform: none;
    }

    .guest-counter .count {
        min-width: 24px;
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    /* Room Type Options */
    .room-type-options {
        display: flex;
        flex-direction: column;
        gap: 0;
        max-height: 300px;
        overflow-y: auto;
    }

    .room-type-option {
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        margin: 0;
        transition: background-color 0.2s ease;
    }

    .room-type-option:last-child {
        border-bottom: none;
    }

    .room-type-option:hover {
        background-color: #f8f9fa;
    }

    .room-type-option input[type="radio"] {
        margin-right: 12px;
        width: 18px;
        height: 18px;
        accent-color: #6366f1;
    }

    .room-type-label {
        font-size: 15px;
        color: #333;
        font-weight: 500;
        flex: 1;
    }

    .room-type-option input[type="radio"]:checked + .room-type-label {
        color: #6366f1;
        font-weight: 600;
    }

    .filter-footer {
        padding: 16px 24px;
        border-top: 1px solid #e5e5e5;
        background-color: #fafafa;
    }

    .btn-filter-apply {
        width: 100%;
        padding: 12px 24px;
        background-color: #6366f1;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-filter-apply:hover {
        background-color: #5856eb;
        transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .date-range-picker,
        .guest-picker,
        .room-type-picker {
            min-width: 100%;
            margin-bottom: 10px;
        }

        .custom-popover {
            min-width: 280px;
            max-width: 90vw;
        }
    }
</style>

<style>
    /* Add to cart and buy now button loading styles */
    .button-add-to-cart, .button-buy-now {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .button-add-to-cart:hover {
        color: #007bff;
        transform: scale(1.1);
    }

    .button-buy-now:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .button-add-to-cart.btn-disabled, .button-buy-now.btn-disabled {
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
    }

    .button-add-to-cart.btn-disabled {
        color: #6c757d;
    }

    .button-add-to-cart.btn-disabled:hover, .button-buy-now.btn-disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* Tab content expand/collapse styles */
    .tab-content-wrapper {
        position: relative;
    }

    .btn-expand-content {
        position: absolute;
        top: 70px;
        right: 20px;
        background: none;
        border: none;
        color: gray;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .btn-expand-content:hover {
        transform: scale(1.1);
    }

    .btn-expand-content.expanded {
        transform: none;
    }

    .btn-expand-content.expanded:hover {
        transform: scale(1.1);
    }

    .tab-content-preview {
        position: relative;
        padding-right: 15px;
    }

    .tab-content-full {
        padding-right: 15px;
    }

    .fade-transition {
        transition: opacity 0.3s ease-in-out;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-expand-content {
            top: 5px;
            right: 5px;
        }

        .tab-content-preview,
        .tab-content-full {
            padding-right: 40px;
        }
    }

    /* Smooth height transition for better UX */
    .tab-content-wrapper {
        overflow: hidden;
    }

    /* Style for the "more questions" indicator */
    /*.text-muted {*/
    /*    font-style: italic;*/
    /*    padding: 10px 15px;*/
    /*    background-color: #f8f9fa;*/
    /*    border-radius: 5px;*/
    /*    border-left: 3px solid #dee2e6;*/
    /*}*/

    /* Loading state for buy now button */
    .button-buy-now.btn-disabled {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }
</style>

<div class="container py-0 pb-md-4 product-page">
    <div class="product-images bg-white rounded-20 p-4 mb-3">
        <!-- Gallery -->
        <div class="hotel-gallery row g-2">
            <div class="col-md-6 mb-3">
                <a href="{{ RvMedia::url($product->image) }}"
                   class="gallery-item main-img d-block position-relative">
                    <img src="{{ RvMedia::url($product->image) }}" alt="{{ $product->name }}"
                         class="img-fluid rounded-10">
                </a>
            </div>
            <div class="col-md-6 d-flex flex-wrap mobile-hidden">
                @foreach($productImages as $index => $image)
                    @if ($index < 4)
                        <div class="col-6 p-1 pt-0 position-relative">
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

        <!-- Thông tin khách sạn -->
        <div class="hotel-details mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="hotel-name fs-24 fw-600">{{ $product->name }}</h2>
                <span class="hotel-rating">{{ $avgRating }}</span>
            </div>
            <p class="hotel-location fs-21 fw-400"><i class="fa fa-map-marker-alt"></i> {{$product->address}}</p>
            @include(Theme::getThemeNamespace() . '::views.ecommerce.level-star', compact('product'))
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 col-12">
            <div class="product-search bg-white rounded-20 p-4">
                <div class="row g-2 align-items-center">
                    <!-- Date Range Picker -->
                    <div class="col-md-4 col-12">
                        <div class="date-range-picker">
                            <input type="text" class="form-control" id="dateRangePickerProduct"
                                   name="date_range" placeholder="Chọn ngày check-in và check-out" readonly>
                            <input type="hidden" name="start_date" id="start_date_room">
                            <input type="hidden" name="end_date" id="end_date_room">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>

                    <!-- Chọn số lượng người -->
                    <div class="col-md-4 col-12">
                        <div class="guest-picker"
                             data-popover="button"
                             data-popover-target="#guestPopoverProduct">
                            <input type="hidden" name="adults" id="adults" value="1">
                            <input type="hidden" name="children" id="children" value="0">
                            <i class="fa fa-users"></i>
                            <span class="detail">1 Người lớn - 0 Trẻ em</span>
                        </div>
                    </div>

                    <!-- Select hạng phòng -->
                    <div class="col-md-4 col-12">
                        <div class="room-type-picker"
                             data-popover="button"
                             data-popover-target="#roomTypePopoverProduct">
                            <input type="hidden" name="room_group" id="choose_room_group" value="">
                            <i class="fa fa-bed"></i>
                            <span class="room-type-detail">Chọn Hạng phòng</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Nút tìm kiếm -->
                    <div class="col-12">
                        <button class="btn btn-main-color w-100" id="find_rooms" type="button"
                                data-url="{{route("public.ajax.filter-rooms-product",$product->id)}}">Chọn phòng
                        </button>
                    </div>
                </div>
            </div>

            <!-- Guest Popover Content -->
            <div data-popover-id="guestPopoverProduct" class="custom-popover">
                <div class="filter-popover">
                    <div class="filter-header">
                        <h5 class="filter-title">Số Khách Mỗi Phòng</h5>
                        <button type="button" class="btn-close" data-popover-close="" aria-label="Close">×</button>
                    </div>

                    <div class="filter-body">
                        <!-- Guest Options -->
                        <div class="filter-section">
                            <div class="guest-options">
                                <!-- Người lớn -->
                                <div class="guest-item">
                                    <div class="guest-info">
                                        <span class="guest-label">Người lớn</span>
                                        <span class="guest-sublabel">từ 13 tuổi trở lên</span>
                                    </div>
                                    <div class="guest-counter">
                                        <button type="button" class="btn counter-btn minus" data-target="adults">−
                                        </button>
                                        <span class="count" id="adultsCountProduct">1</span>
                                        <button type="button" class="btn counter-btn plus" data-target="adults">+
                                        </button>
                                    </div>
                                </div>

                                <!-- Trẻ em -->
                                <div class="guest-item">
                                    <div class="guest-info">
                                        <span class="guest-label">Trẻ em</span>
                                        <span class="guest-sublabel">Độ tuổi 2 - 12</span>
                                    </div>
                                    <div class="guest-counter">
                                        <button type="button" class="btn counter-btn minus" data-target="children">−
                                        </button>
                                        <span class="count" id="childrenCountProduct">0</span>
                                        <button type="button" class="btn counter-btn plus" data-target="children">+
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-footer">
                        <button type="button" class="btn btn-filter-apply" id="applyGuestBtnProduct">Xong</button>
                    </div>
                </div>
            </div>

            <!-- Room Type Popover Content -->
            <div data-popover-id="roomTypePopoverProduct" class="custom-popover">
                <div class="filter-popover">
                    <div class="filter-header">
                        <h5 class="filter-title">Chọn Hạng Phòng</h5>
                        <button type="button" class="btn-close" data-popover-close="" aria-label="Close">×</button>
                    </div>

                    <div class="filter-body">
                        <!-- Room Type Options -->
                        <div class="filter-section">
                            <div class="room-type-options">
                                <label class="room-type-option">
                                    <input type="radio" name="room_type_option" value="">
                                    <span class="room-type-label">Tất cả hạng phòng</span>
                                </label>
                                @foreach($product->groupedProduct()->orderBy("sale_price","asc")->get() as $k=>$groupedProduct)
                                    <label class="room-type-option">
                                        <input type="radio" name="room_type_option" value="{{$groupedProduct->id}}" {{$k==0?"checked":''}}>
                                        <span class="room-type-label">{{$groupedProduct->name}}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="filter-footer">
                        <button type="button" class="btn btn-filter-apply" id="applyRoomTypeBtnProduct">Xong</button>
                    </div>
                </div>
            </div>
            {{--            Chi tiết giá theo đêm--}}
            <div class="product-prices bg-white rounded-20 p-4 mt-3">
            </div>
            <div class="product-services bg-white rounded-20 p-4 mt-3">
                <h2>Bảng giá {{ $product->name }}</h2>
                <div class="rooms-wrapper" id="list-products-grouped">
                    @foreach($product->groupedProduct as $p=>$item)
                        @php
                            $maxAdults = MetaBox::getMetaData($item, 'max_adults', true);
                            $maxChildren = MetaBox::getMetaData($item, 'max_children', true);
                        @endphp
                        <div class="service-item d-flex align-items-center py-3 {{$p>2?"products-grouped-hide":""}}">
                            <img src="{{ RvMedia::getImageUrl($item->image, null,false,RvMedia::getDefaultImage()) }}"
                                 alt="{{ $item->image }}" class="rounded-10">

                            <div class="room-content flex-grow-1 px-3">
                                <h5 class="fw-600 fs-18 max-2-lines">{{ $item->name }}</h5>
                                <div class="info">
                                    <div class="price-service-product price fw-600 fs-18 me-4">
                                        @php
                                            $currentDatePrice = get_current_date_price($item);
                                            if(!$currentDatePrice){
                                                $currentDatePrice="Hết Phòng";
                                            }else{
                                                $currentDatePrice = format_price($currentDatePrice);
                                            }
                                        @endphp

                                        {{ $currentDatePrice }}
                                    </div>
                                    <div class="fw-500 fs-18 max-people">
                                        Phòng tối đa {{ $maxAdults }} người lớn, {{$maxChildren}} trẻ em
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
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <div id="view-all-product-grouped" class="btn btn-light">Xem tất cả</div>
                </div>

            </div>
            @if ($product->services()->count() > 0)
                <div class="services-product bg-white rounded-20 p-4 mt-3">
                    <h2>Dịch vụ đi kèm</h2>
                    <div class="list-service">
                        @foreach($product->services as $k=>$service)
                            <div
                                class="service-item d-flex justify-content-between align-items-center py-2 border-bottom {{$k>2?"services-hide":""}}">
                                <div class="service-info">
                                    <div class="d-flex align-items-center py-3">
                                        <img style="width: 140px;
    height: 100px;
    object-fit: cover;"
                                             src="{{ RvMedia::getImageUrl($service->image, null,false,RvMedia::getDefaultImage()) }}"
                                             alt="{{ $service->name }}" class="rounded-10">

                                        <div class="room-content flex-grow-1 px-3">
                                            <h5 class="fw-600 fs-18 max-2-lines">{{ $service->name }}</h5>
                                            <div class="info">
                                                <div class="price-service-product price fw-600 fs-18 me-4">
                                                    {{ format_price($service->price) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="service-quantity">
                                    <div class="counter d-flex align-items-center gap-1">
                                        <button type="button" class="btn counter-btn minus"
                                                data-service-id="{{$service->id}}">-
                                        </button>
                                        <input type="number" hidden=""
                                               class="form-control form-control-sm mx-2 text-center service_quantity"
                                               name="services[{{$service->id}}]"
                                               data-service-id="{{$service->id}}"
                                               data-extra-price="{{$service->price}}"
                                               id="service_{{$k}}"
                                               value="0"
                                               min="0"
                                               max="99"
                                               style="width: 60px;">
                                        <span class="count_service">0</span>
                                        <button type="button" class="btn counter-btn plus"
                                                data-service-id="{{$service->id}}">+
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <div id="view-all-service" class="btn btn-light">Xem tất cả</div>
                    </div>
                </div>
            @endif
            <div class="product-services-details mt-3">
                <h2>Chi tiết</h2>
                <div class="swiper product-detail-swiper">
                    <div class="swiper-wrapper">
                        @foreach($product->groupedProduct as $item)
                            @php
                                $maxAdults = MetaBox::getMetaData($item, 'max_adults', true);
                                $maxChildren = MetaBox::getMetaData($item, 'max_children', true);
                            @endphp
                                <!-- Bắt đầu sản phẩm -->
                            <div class="swiper-slide p-1 p-md-0">
                                <div class="product-item-v2 bg-white rounded-20 shadow-sm">

                                    <a href="{{ $product->url }}">
                                        <img
                                            src="{{ RvMedia::getImageUrl($item->image, null,false,RvMedia::getDefaultImage()) }}"
                                            class="img-fluid top-rounded-20" alt="{{$item->name}}">
                                    </a>
                                    <div class="p-3">
                                        <h5 class="fw-600 fs-16">
                                            <a href="{{ $item->url }}"
                                               class="text-decoration-none text-dark title-product">{{ $item->name }}</a>
                                        </h5>
                                        <div class="rating text-warning my-2">
                                            @include(Theme::getThemeNamespace() . '::views.ecommerce.level-star', compact('product'))
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">

                                            <p class="text-muted mb-0 fs-14 item-address">
                                                <i class="fa-solid fa-map-marker-alt"></i> {{$product->address}}
                                            </p>
                                            <span class="product-price-badge fs-16 fw-700">
                                                @php
                                                    $currentDatePrice = get_current_date_price($item);
                                                @endphp
                                                {{ format_price($currentDatePrice) }}
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Kết thúc sản phẩm -->
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="product-intro bg-white rounded-20 p-4 mt-3 text-center">
                <img src="{{ Theme::asset()->url('images/products/hotel-intro.png') }}" class="intro-img" alt="intro">
                <h3 class="fw-600 fs-20">{{ $product->name }}</h3>
                <div class="fs-16 fw-400 px-4 px-md-5 content">{!! BaseHelper::clean($product->description) !!}
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
            <div class="product-reviews bg-white rounded-20 py-4 px-3 px-md-5 mt-3">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h2>Xếp hạng nhận xét</h2>
                        <div class="review-point">{{ $avgRating*2}}</div>
                        <div class="text-muted desktop-hidden">Chạm để xếp hạng</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-end">
                            <div class="rating-summary">
                                <div class="d-flex justify-content-end mb-2">
                                    <a href="{{route("public.reviews")}}" class="text-primary small">Xem tất cả</a>
                                </div>

                                <div class="rating-bars">

                                    @foreach (EcommerceHelper::getReviewsGroupedByProductId($product->id, $product->reviews_count) as $item)
                                        <div class="rating-bar">
                                            <span class="stars">{{ str_repeat('★', $item['star']) }}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ $item['percent'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{--                                <div class="text-end mt-2">--}}
                                {{--                                    <span class="avg-stars">★★★★★</span>--}}
                                {{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-primary write-reviews">
                    <i class="fa-solid fa-pen-to-square "></i>
                    Viết nhận xét
                </div>
                <div class="form-reviews d-none">
                    <form action="{{ route('public.reviews.create') }}" method="post" enctype="multipart/form-data"
                          class="p-4 border rounded shadow-sm bg-white">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <h5 class="mb-3 fw-bold text-primary">Đánh giá sản phẩm</h5>

                        <!-- Star Rating -->
                        <div class="mb-3 text-center">
                            <label class="form-label fw-semibold">Đánh giá:</label>
                            <div class="star-rating d-inline-flex">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" {{ old('star') == "$i" ? 'checked' : '' }} name="star"
                                           id="star{{ $i }}" value="{{ $i }}">
                                    <label for="star{{ $i }}"><i class="fa fa-star fa-1x"></i></label>
                                @endfor
                            </div>
                            @error('star')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chọn ảnh minh họa:</label>
                            <input type="file" name="images[]" class="form-control" multiple>
                            @error('images')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nhận xét của bạn:</label>
                            <textarea class="form-control" name="comment" rows="4"
                                      placeholder="Nhập nhận xét...">{{ old('comment') }}</textarea>
                            @error('comment')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Gửi đánh giá</button>
                    </form>

                    <!-- CSS -->
                    <style>
                        .star-rating {
                            direction: rtl;
                        }

                        .star-rating input {
                            display: none;
                        }

                        .star-rating label {
                            font-size: 2rem;
                            color: #ccc;
                            cursor: pointer;
                            transition: color 0.2s;
                            margin-right: 0.25rem;
                        }

                        .star-rating label:hover,
                        .star-rating label:hover ~ label {
                            color: gold;
                        }

                        .star-rating input:checked ~ label {
                            color: #ccc;
                        }

                        .star-rating input:checked + label,
                        .star-rating input:checked + label ~ label {
                            color: gold;
                        }
                    </style>

                </div>
                <div class="reviews">
                    @foreach(EcommerceHelper::getProductReviews($product) as $review)
                        <div class="review-item rounded-10 mb-3">
                            <div class="review-name">{{ $review->userName }}</div>
                            @include(Theme::getThemeNamespace() . '::views.ecommerce.product-star-rating', ['rating' => $review->star])
                            <div class="review-detail">{!! BaseHelper::clean($review->comment) !!}
                            </div>
                            <div class="review-images">
                                @if ($review->images)
                                    <div class="review-item__images mt-3">
                                        <div class="row g-1 review-images">
                                            @foreach ($review->images as $image)
                                                <a href="{{ RvMedia::getImageUrl($image) }}"
                                                   class="col-3 col-md-2 col-xl-1 position-relative">
                                                    <img src="{{ RvMedia::getImageUrl($image, 'thumb') }}"
                                                         alt="{{ $review->comment }}" class="img-thumbnail">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <style>
                .tab-content-full img,.tab-content-preview img{
                    border-radius: 15px;
                }
            </style>
            <div
                class="product-blog product-tab-infomation bg-white rounded-20 py-4 px-3 px-md-5 mt-3 position-relative">
                <div class="mb-3">
                    <ul class="nav nav-pills gap-3 mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="border-0 active  text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge"
                                id="pills-content-tab" data-bs-toggle="pill" data-bs-target="#pills-content"
                                type="button"
                                role="tab" aria-controls="pills-content" aria-selected="true">Nội dung
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="border-0 text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge"
                                id="pills-chinh-sach-tab" data-bs-toggle="pill" data-bs-target="#pills-chinh-sach"
                                type="button" role="tab" aria-controls="pills-chinh-sach-tab" aria-selected="false">
                                Chính sách
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="border-0 text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge"
                                id="pills-question-tab" data-bs-toggle="pill" data-bs-target="#pills-question"
                                type="button" role="tab" aria-controls="pills-question" aria-selected="false">Câu
                                hỏi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="border-0 text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge"
                                id="pills-note-tab" data-bs-toggle="pill" data-bs-target="#pills-note" type="button"
                                role="tab" aria-controls="pills-note" aria-selected="false">Lưu ý
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-content" role="tabpanel"
                             aria-labelledby="pills-content-tab">
                            <div class="tab-content-wrapper">
                                @php
                                    $isContentLong = !empty($product->description);
                                @endphp
                                <div
                                    class="tab-content-preview">{!!$product->description !!}</div>
                                @if($isContentLong)
                                    <div class="tab-content-full"
                                         style="display: none;">{!! $product->content !!}</div>

                                @endif
                            </div>

                            <button class="btn-expand-content" data-target="pills-content"
                                    title="Xem toàn bộ nội dung">
                                <i class="fa-solid fa-circle-plus fs-22"></i>
                            </button>
                        </div>
                        @foreach($product->metadata as $k=>$metaProduct)
                            @switch($metaProduct->meta_key)
                                @case("policy")
                                    <div class="tab-pane fade" id="pills-chinh-sach" role="tabpanel"
                                         aria-labelledby="pills-chinh-sach-tab">
                                        <div class="tab-content-wrapper">
                                            @php
                                                $policyText = strip_tags($metaProduct->meta_value[0]??"");
                                                $isPolicyLong = strlen($policyText) > 400;
                                            @endphp
                                            <div
                                                class="tab-content-preview">{!!   nl2br($isPolicyLong ? Str::limit($policyText, 400) : $metaProduct->meta_value[0]??"") !!}</div>
                                            @if($isPolicyLong)
                                                <div class="tab-content-full"
                                                     style="display: none;">{!! nl2br($metaProduct->meta_value[0]??"") !!}</div>

                                            @endif
                                        </div>
                                        <button class="btn-expand-content" data-target="pills-chinh-sach"
                                                title="Xem toàn bộ chính sách">
                                            <i class="fa-solid fa-circle-plus fs-22"></i>
                                        </button>
                                    </div>
                                    @break
                                @case("faq_schema_config")

                                    <div class="tab-pane fade" id="pills-question" role="tabpanel"
                                         aria-labelledby="pills-question-tab">
                                        <div class="tab-content-wrapper">
                                            @php
                                                $faqCount = count($metaProduct->meta_value[0]??[]);
                                                $hasManyFaqs = $faqCount > 2;
                                            @endphp
                                            <div class="tab-content-preview">
                                                @if($faqCount > 0)
                                                    <div class="faqs">
                                                        <div class="accordion" id="accordionPreview">
                                                            @foreach(array_slice($metaProduct->meta_value[0]??[], 0, 2) as $i=>$meta_value)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="preview-heading{{$i}}">
                                                                        <button
                                                                            class="accordion-button bg-light text-dark collapsed"
                                                                            type="button"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#preview-collapse{{$i}}"
                                                                            aria-expanded="false"
                                                                            aria-controls="preview-collapse{{$i}}">
                                                                            {{nl2br($meta_value[0]["value"]??"")}}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="preview-collapse{{$i}}"
                                                                         class="accordion-collapse collapse"
                                                                         aria-labelledby="preview-heading{{$i}}">
                                                                        <div class="accordion-body">
                                                                            {!!  nl2br(Str::limit(strip_tags($meta_value[1]["value"]??""), 100)) !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if($hasManyFaqs)
                                                                <div class="text-muted mt-2">... và {{ $faqCount - 2 }}
                                                                    câu hỏi khác
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($hasManyFaqs)
                                                <div class="tab-content-full" style="display: none;">
                                                    <div class="faqs">
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            @foreach($metaProduct->meta_value[0]??[] as $i=>$meta_value)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="panelsStayOpen-heading{{$i}}">
                                                                        <button
                                                                            class="accordion-button bg-light text-dark"
                                                                            type="button"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#panelsStayOpen-collapse{{$i}}"
                                                                            aria-expanded="true"
                                                                            aria-controls="panelsStayOpen-collapse{{$i}}">
                                                                            {{nl2br($meta_value[0]["value"]??"")}}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="panelsStayOpen-collapse{{$i}}"
                                                                         class="accordion-collapse collapse show"
                                                                         aria-labelledby="panelsStayOpen-heading{{$i}}">
                                                                        <div class="accordion-body">
                                                                            {!! nl2br($meta_value[1]["value"]??"")!!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <button class="btn-expand-content" data-target="pills-question"
                                                title="Xem tất cả câu hỏi">
                                            <i class="fa-solid fa-circle-plus fs-22"></i>
                                        </button>
                                    </div>
                                    @break
                                @case("rule")
                                    <div class="tab-pane fade" id="pills-note" role="tabpanel"
                                         aria-labelledby="pills-note-tab">
                                        <div class="tab-content-wrapper">
                                            @php
                                                $ruleText = strip_tags($metaProduct->meta_value[0]??"");
                                                $isRuleLong = strlen($ruleText) > 400;
                                            @endphp
                                            <div
                                                class="tab-content-preview">{!!  nl2br($isRuleLong ? Str::limit($ruleText, 400) : $metaProduct->meta_value[0]??"") !!}</div>
                                            @if($isRuleLong)
                                                <div class="tab-content-full"
                                                     style="display: none;">{!! nl2br($metaProduct->meta_value[0]??"")!!}</div>

                                            @endif
                                        </div>
                                        <button class="btn-expand-content" data-target="pills-note"
                                                title="Xem toàn bộ lưu ý">
                                            <i class="fa-solid fa-circle-plus fs-22"></i>
                                        </button>
                                    </div>
                                    @break
                            @endswitch
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 col-12 total-result " style="display: none">
        </div>
    </div>
</div>
</div>

{!! do_shortcode('[homepage-section-4][/homepage-section-4]') !!}
{!! do_shortcode('[homepage-section-3][/homepage-section-3]') !!}
