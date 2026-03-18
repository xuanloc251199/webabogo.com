<div class="section-5">
    <!-- Khối Hệ thống đặt phòng chất lượng -->
    <div class="container py-5">
        <h3 class="mb-4 fw-bold">{{ $shortcode->title ?? 'Hệ thống đặt phòng chất lượng' }}</h3>

        <div class="row align-items-center">
            <!-- Cột bên trái -->
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="d-flex flex-column align-items-center bg-white shadow-sm rounded-20">
                    <a class="navbar-brand me-3 site-logo fs-41 fw-500 text-dark" href="/">
                        abogo
                    </a>
                    <div class="text-center">
                        <p class="fs-16 fw-600 mb-2">{{ $shortcode->desc ?? 'Trải nghiệm đặt phòng dễ dàng, nhanh chóng với hệ thống tiên tiến nhất.' }}
                        </p>
                    </div>
                    <a href="{{ $shortcode->link }}" class="text-decoration-none mb-3 view-more">Xem thêm<i
                            class="fa-solid fa-angles-right"></i> </a>
                    <img src="{{ $shortcode->image ? RvMedia::getImageUrl($shortcode->image) : Theme::asset()->url('images/products/product-image.png') }}" alt="Hình ảnh minh họa"
                         class="img-fluid rounded-20 mb-3" style="max-width: 320px;">
                </div>
            </div>

            <!-- Cột bên phải -->
            <div class="col-md-6">
                <div class="d-flex align-items-center p-3 mb-3 bg-white shadow-sm rounded-20">
                    <img src="{{ $shortcode->image_1 ? RvMedia::getImageUrl($shortcode->image_1) : Theme::asset()->url('images/common/sup-1.png') }}" alt="Dịch vụ 5 sao"
                         class="me-5 rounded-10" style="width: 86px; height: 86px;">
                    <div>
                        <h5 class="fw-400 fs-18 mb-1 text-center">{{ $shortcode->title_1 ?? 'Abogo đáp ứng mọi nhu cầu cho kì nghỉ du lịch của bạn' }}</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center p-3 mb-3 bg-white shadow-sm rounded-20">
                    <img src="{{ $shortcode->image_2 ? RvMedia::getImageUrl($shortcode->image_2) : Theme::asset()->url('images/common/sup-2.png') }}" alt="Chất lượng hàng đầu"
                         class="me-5 rounded-10" style="width: 86px; height: 86px;">
                    <div>
                        <h5 class="fw-400 fs-18 mb-1 text-center">{{ $shortcode->title_2 ?? 'Bạn muốn sử dụng dịch vụ tốt. Nhu cầu Abogo là được phục vụ bạn' }}</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center p-3 bg-white shadow-sm rounded-20">
                    <img src="{{ $shortcode->image_3 ? RvMedia::getImageUrl($shortcode->image_3) : Theme::asset()->url('images/common/sup-3.png') }}" alt="Tư vấn 24/7"
                         class="me-5 rounded-10" style="width: 86px; height: 86px;">
                    <div>
                        <h5 class="fw-400 fs-18 mb-1 text-center">{{ $shortcode->title_3 ?? 'Thanh toán an toàn thuận tiên linh hoạt và giá luôn đảm bảo chất lượng' }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
