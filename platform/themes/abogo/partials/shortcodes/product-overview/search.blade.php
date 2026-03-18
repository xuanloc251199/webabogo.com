@php
    [$categories, $brands, $tags, $rand, $categoriesRequest, $urlCurrent, $categoryId, $maxFilterPrice] = EcommerceHelper::dataForFilter($category ?? null);
    $extentions = \Botble\Extensions\Models\Extensions::query()->pluck("name","id")->toArray();

@endphp
<style>
    .btn-close:before {
        content: '';
    }
</style>
<form action="{{ $shortcode->route_action_form ?? route('public.products') }}" class="product-search-section container"
      method="get">

    <div class="rounded-20 p-3 bg-white form-search-product-category">
        @foreach($categoriesRequest as $catId)
            <input type="hidden" name="categories[]" value="{{ $catId }}">
            <input type="hidden" name="filter_by_availability" value="1">
        @endforeach
        <div class="row align-items-center justify-content-center">

            <!-- Ô tìm kiếm -->
            <div class="col-md-auto col-12">
                <div class="search-input">
                    <i class="fa fa-search"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." name="q"
                           value="">
                </div>
            </div>

            <!-- Date Range Picker -->
            <div class="col-md-auto col-12">
                <div class="date-range-picker">
                    <input type="text" class="form-control" id="dateRangePicker"
                           name="date_range" placeholder="Chọn ngày check-in và check-out" readonly>
                    <input type="hidden" name="start_date" id="startDate">
                    <input type="hidden" name="end_date" id="endDate">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>

            <!-- Chọn số lượng người/phòng -->
            <div class="col-md-auto col-12">
                <div class="guest-picker"
                     data-popover="button"
                     data-popover-target="#guestPopover">
                    <input type="hidden" name="rooms" value="1">
                    <input type="hidden" name="adults" value="1">
                    <input type="hidden" name="children" value="0">
                    <i class="fa fa-users"></i>
                    <span class="detail">1 Phòng - 1 Người lớn - 0 Trẻ em</span>
                </div>
            </div>
            <!-- Select địa điểm -->
            <div class="d-flex col-md-auto  col-12 gap-2">
                <div class="col-md-auto col">
                    <div class="btn location-picker"
                         data-popover="button"
                         data-popover-target="#locationPopover">
                        <input type="hidden" name="places[]" value="">
                        <i class="fa fa-map-marker-alt"></i>
                        <span class="location-detail">Địa điểm</span>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Bộ lọc & Sắp xếp -->
                <div class="col-md-auto col">
                    <button type="button" class="btn filter-btn" id="filterButton"
                            data-popover="button"
                            data-popover-target="#filterPopover">
                        <i class="fa fa-filter"></i> Bộ lọc
                    </button>
                </div>
                <div class="col-md-auto col">
                    <button type="button" class="btn sort-btn"
                            data-popover="button"
                            data-popover-target="#sortPopover">
                        <i class="fa fa-sort"></i> Sắp xếp
                    </button>
                </div>
            </div>

            <!-- Nút tìm kiếm -->
            <div class="col-md-auto col-12">
                <button type="submit" class="btn btn-main-color"><i class="fa fa-search"></i></button>
            </div>
        </div>


        <!-- Filter Popover Content (Hidden) -->
        <div data-popover-id="filterPopover" class="custom-popover">
            <div class="filter-popover">
                <div class="filter-header">
                    <h5 class="filter-title">Bộ Lọc</h5>
                    <button type="button" class="btn-close" data-popover-close aria-label="Close">×</button>
                </div>

                <div class="filter-body">
                    <!-- Giá theo đêm -->
                    <div class="filter-section">
                        <h6 class="filter-section-title">Giá theo đêm</h6>
                        <p class="filter-section-subtitle">Chưa bao gồm phí và thuế</p>
                        <div class="price-range-container">
                            <div class="price-range-slider">
                                <input type="range" id="priceRangeMin" class="form-range" min="0" max="30000000"
                                       value="0"
                                       step="100000">
                                <input type="range" id="priceRangeMax" class="form-range" min="0" max="30000000"
                                       value="30000000" step="100000">
                            </div>
                            <div class="price-range-values">
                                <div class="price-min">
                                    <label>Giá tối thiểu</label>
                                    <span id="priceMinValue">0đ</span>
                                </div>
                                <div class="price-max">
                                    <label>Giá tối đa</label>
                                    <span id="priceMaxValue">16.4.000.000đ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Số phòng -->
                    {{--                <div class="filter-section">--}}
                    {{--                    <h6 class="filter-section-title">Số phòng</h6>--}}
                    {{--                    <div class="room-options">--}}
                    {{--                        <label class="room-option">--}}
                    {{--                            <input type="radio" name="rooms_radio" value="" checked="">--}}
                    {{--                            <span class="room-label">Bất kì</span>--}}
                    {{--                        </label>--}}
                    {{--                        <label class="room-option">--}}
                    {{--                            <input type="radio" name="rooms_radio" value="1">--}}
                    {{--                            <span class="room-label">1</span>--}}
                    {{--                        </label>--}}
                    {{--                        <label class="room-option">--}}
                    {{--                            <input type="radio" name="rooms_radio" value="2">--}}
                    {{--                            <span class="room-label">2</span>--}}
                    {{--                        </label>--}}
                    {{--                        <label class="room-option">--}}
                    {{--                            <input type="radio" name="rooms_radio" value="3">--}}
                    {{--                            <span class="room-label">3</span>--}}
                    {{--                        </label>--}}
                    {{--                        <label class="room-option">--}}
                    {{--                            <input type="radio" name="rooms_radio" value="4">--}}
                    {{--                            <span class="room-label active">4</span>--}}
                    {{--                        </label>--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}

                    <!-- Tiện ích phòng -->
                    <div class="filter-section">
                        <h6 class="filter-section-title">Tiện ích phòng</h6>
                        <div class="amenity-options">
                            @foreach($extentions as $id => $extention)
                                <label class="amenity-option">
                                    <input type="checkbox" name="room_amenities" value="{{$id}}">
                                    <span class="amenity-label">{{$extention}}</span>
                                </label>
                            @endforeach
                        </div>
                        <a href="#" class="show-more-link">Xem thêm</a>
                    </div>

                    <!-- Hạng sao -->
                    <div class="filter-section">
                        <h6 class="filter-section-title">Hạng sao</h6>
                        <div class="star-rating-options">
                            <label class="star-option">
                                <input type="radio" name="level_star" value="">
                                <span>Tất cả</span>
                            </label>
                            <label class="star-option">
                                <input type="radio" name="level_star" value="5">
                                <span class="stars">★★★★★</span>
                            </label>
                            <label class="star-option">
                                <input type="radio" name="level_star" value="4">
                                <span class="stars">★★★★</span>
                            </label>
                            <label class="star-option">
                                <input type="radio" name="level_star" value="3">
                                <span class="stars">★★★</span>
                            </label>
                            <label class="star-option">
                                <input type="radio" name="level_star" value="2">
                                <span class="stars">★★</span>
                            </label>
                            <label class="star-option">
                                <input type="radio" name="level_star" value="1">
                                <span class="stars">★</span>
                            </label>
                        </div>
                    </div>

                    {{--                <!-- Tiện ích khách sạn -->--}}
                    {{--                <div class="filter-section">--}}
                    {{--                    <h6 class="filter-section-title">Tiện ích khách sạn</h6>--}}
                    {{--                    <div class="hotel-amenity-options">--}}
                    {{--                        <label class="hotel-amenity-option">--}}
                    {{--                            <input type="checkbox" name="hotel_amenities" value="pet_friendly">--}}
                    {{--                            <span class="hotel-amenity-label">Cho Phép Mang Theo Thú Cưng</span>--}}
                    {{--                        </label>--}}
                    {{--                        <label class="hotel-amenity-option">--}}
                    {{--                            <input type="checkbox" name="hotel_amenities" value="breakfast_included">--}}
                    {{--                            <span class="hotel-amenity-label">Bao Gồm Bữa Ăn Sáng</span>--}}
                    {{--                        </label>--}}
                    {{--                        <label class="hotel-amenity-option">--}}
                    {{--                            <input type="checkbox" name="hotel_amenities" value="swimming_pool">--}}
                    {{--                            <span class="hotel-amenity-label">Có Hồ Bơi</span>--}}
                    {{--                        </label>--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                </div>

                <div class="filter-footer">
                    <button type="button" class="btn btn-filter-apply" id="applyAmenityBtn">Xong</button>
                </div>
            </div>
        </div>

        <!-- Location Popover Content -->
        <div data-popover-id="locationPopover" class="custom-popover">
            <div class="filter-popover">
                <div class="filter-header">
                    <h5 class="filter-title">Lọc Địa Điểm</h5>
                    <button type="button" class="btn-close" data-popover-close="" aria-label="Close">×</button>
                </div>

                <div class="filter-body">
                    <!-- Location Options -->
                    <div class="filter-section">
                        <div class="location-options">
                            <label class="location-option">
                                <input type="radio" name="location_option" value="" checked>
                                <span class="location-label">Tất cả địa điểm</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="1">
                                <span class="location-label">Sapa</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="9">
                                <span class="location-label">Hạ Long</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="8">
                                <span class="location-label">Hà Nội</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="13">
                                <span class="location-label">Ninh Bình</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="3">
                                <span class="location-label">Gia Lai</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="6">
                                <span class="location-label">Đà Nẵng</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="7">
                                <span class="location-label">Hội An</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="5">
                                <span class="location-label">Đà Lạt</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="4">
                                <span class="location-label">Phú Quốc</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="12">
                                <span class="location-label">Huế</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="14">
                                <span class="location-label">Hà Giang</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="2">
                                <span class="location-label">HCM</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="11">
                                <span class="location-label">Nha Trang</span>
                            </label>
                            <label class="location-option">
                                <input type="radio" name="location_option" value="10">
                                <span class="location-label">Vũng Tàu</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="filter-footer">
                    <button type="button" class="btn btn-filter-apply" id="applyLocationBtn">Xong</button>
                </div>
            </div>
        </div>

        <!-- Guest Popover Content -->
        <div data-popover-id="guestPopover" class="custom-popover">
            <div class="filter-popover">
                <div class="filter-header">
                    <h5 class="filter-title">Số Khách Mỗi Phòng</h5>
                    <!-- <button type="button" class="btn-cancel guest-reset-btn" aria-label="Reset">Xóa</button> -->
                    <button type="button" class="btn-close" data-popover-close="" aria-label="Close">×</button>
                </div>

                <div class="filter-body">
                    <!-- Guest Options -->
                    <div class="filter-section">
                        <div class="guest-options">
                            <!-- Phòng -->
                            <div class="guest-item">
                                <div class="guest-info">
                                    <span class="guest-label">Phòng</span>
                                </div>
                                <div class="guest-counter">
                                    <button type="button" class="btn counter-btn minus" data-target="rooms">−</button>
                                    <span class="count" id="roomsCount">1</span>
                                    <button type="button" class="btn counter-btn plus" data-target="rooms">+</button>
                                </div>
                            </div>

                            <!-- Người lớn -->
                            <div class="guest-item">
                                <div class="guest-info">
                                    <span class="guest-label">Người lớn</span>
                                    <span class="guest-sublabel">từ 13 tuổi trở lên</span>
                                </div>
                                <div class="guest-counter">
                                    <button type="button" class="btn counter-btn minus" data-target="adults">−</button>
                                    <span class="count" id="adultsCount">1</span>
                                    <button type="button" class="btn counter-btn plus" data-target="adults">+</button>
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
                                    <span class="count" id="childrenCount">0</span>
                                    <button type="button" class="btn counter-btn plus" data-target="children">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-footer">
                    <button type="button" class="btn btn-filter-apply" id="applyGuestBtn">Xong</button>
                </div>
            </div>
        </div>

        <!-- Sort Popover Content -->
        <div data-popover-id="sortPopover" class="custom-popover">
            <div class="filter-popover">
                <div class="filter-header">
                    <h5 class="filter-title">Sắp Xếp</h5>
                    <button type="button" class="btn-cancel" data-popover-close aria-label="Close">Hủy</button>
                </div>

                <div class="filter-body">
                    <!-- Sort Options -->
                    <div class="filter-section">
                        <div class="sort-options">
                            {{--                        <label class="sort-option">--}}
                            {{--                            <input type="radio" name="sort_by" value="relevant" checked>--}}
                            {{--                            <span class="sort-label">Phù hợp nhất</span>--}}
                            {{--                        </label>--}}
                            <label class="sort-option">
                                <input type="radio" name="sort_by" value="price_asc">
                                <span class="sort-label">Từ thấp đến cao</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="sort_by" value="price_desc">
                                <span class="sort-label">Từ cao đến thấp</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="sort_by" value="level_star_desc">
                                <span class="sort-label">Hạng sao Cao → Thấp</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="sort_by" value="level_star_asc">
                                <span class="sort-label">Hạng sao Thấp → Cao</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="sort_by" value="rating_asc">
                                <span class="sort-label">Đánh giá Thấp → Cao</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="sort_by" value="rating_desc">
                                <span class="sort-label">Đánh giá Cao → Thấp</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="filter-footer">
                    <button type="button" class="btn btn-filter-apply" id="applySortBtn">Xong</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Filter Styles -->
<style>
    .filter-popover {
        width: 400px;
        max-height: 600px;
        overflow-y: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        margin: 0;
        color: #333;
    }

    .filter-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #666;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-close:hover {
        color: #333;
    }

    .filter-body {
        padding: 0 24px;
    }

    .filter-section {
        padding: 20px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .filter-section:last-child {
        border-bottom: none;
    }

    .filter-section-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: #333;
    }

    .filter-section-subtitle {
        font-size: 14px;
        color: #666;
        margin: 0 0 16px 0;
    }

    /* Price Range Styles */
    .price-range-container {
        position: relative;
    }

    .price-range-slider {
        position: relative;
        height: 20px;
        margin: 20px 0;
    }

    .price-range-slider input[type="range"] {
        position: absolute;
        width: 100%;
        height: 6px;
        background: none;
        pointer-events: none;
        -webkit-appearance: none;
    }

    .price-range-slider input[type="range"]::-webkit-slider-track {
        height: 6px;
        background: #ddd;
        border-radius: 3px;
    }

    .price-range-slider input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: #6366f1;
        cursor: pointer;
        pointer-events: all;
        position: relative;
        z-index: 2;
    }

    .price-range-slider input[type="range"]::-moz-range-track {
        height: 6px;
        background: #ddd;
        border-radius: 3px;
        border: none;
    }

    .price-range-slider input[type="range"]::-moz-range-thumb {
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: #6366f1;
        cursor: pointer;
        pointer-events: all;
        border: none;
    }

    .price-range-values {
        display: flex;
        justify-content: space-between;
        margin-top: 16px;
    }

    .price-min, .price-max {
        text-align: center;
    }

    .price-min label, .price-max label {
        display: block;
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }

    .price-min span, .price-max span {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    /* Room Options */
    .room-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .room-option {
        cursor: pointer;
    }

    .room-option input[type="radio"] {
        display: none;
    }

    .room-label {
        display: inline-block;
        padding: 8px 16px;
        border: 2px solid #e5e5e5;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        color: #666;
        transition: all 0.2s ease;
        min-width: 40px;
        text-align: center;
    }

    .room-option input[type="radio"]:checked + .room-label,
    .room-label.active {
        background: #2c3e50;
        color: white;
        border-color: #2c3e50;
    }

    /* Amenity Options */
    .amenity-options {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .amenity-option {
        cursor: pointer;
        display: flex;
        align-items: center;
        width: calc(50% - 6px);
    }

    .amenity-option input[type="checkbox"] {
        margin-right: 8px;
        width: 16px;
        height: 16px;
    }

    .amenity-label {
        font-size: 14px;
        color: #333;
    }

    .show-more-link {
        color: #6366f1;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .show-more-link:hover {
        text-decoration: underline;
    }

    /* Star Rating */
    .star-rating-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .star-option {
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .star-option input[type="radio"] {
        margin-right: 12px;
        width: 16px;
        height: 16px;
    }

    .stars {
        color: #ffc107;
        font-size: 16px;
    }

    /* Hotel Amenities */
    .hotel-amenity-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .hotel-amenity-option {
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .hotel-amenity-option input[type="checkbox"] {
        margin-right: 12px;
        width: 16px;
        height: 16px;
    }

    .hotel-amenity-label {
        font-size: 14px;
        color: #333;
    }

    /* Filter Footer */
    .filter-footer {
        padding: 20px 24px;
        border-top: 1px solid #e5e5e5;
    }

    .btn-filter-apply {
        width: 100%;
        background: #6366f1;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        transition: background-color 0.2s ease;
    }

    .btn-filter-apply:hover {
        background: #5145d4;
    }

    /* Custom Popover Styles */
    .popover {
        border: none !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        border-radius: 12px !important;
        max-width: 450px !important;
    }

    .popover-body {
        padding: 0 !important;
    }

    .popover .popover-arrow {
        display: none !important;
    }
</style>

@php
    Theme::asset()->add('filter-popover-js', 'js/filter-popover.js');
@endphp
