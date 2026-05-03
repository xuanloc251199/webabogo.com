{{-- Mobile-only hero header (Liquid Glass page title) — design polish, no DB content --}}
<div class="mobile-hero desktop-hidden">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="mobile-hero-title mb-0">Sản phẩm</h1>
        @if (auth('customer')->check())
            <a href="{{ route('customer.overview') }}" class="mobile-hero-avatar" aria-label="{{ auth('customer')->user()->name }}">
                <i class="fa-solid fa-user"></i>
            </a>
        @else
            <a href="{{ route('customer.login') }}" class="mobile-hero-avatar" aria-label="Đăng nhập">
                <i class="fa-solid fa-user"></i>
            </a>
        @endif
    </div>
</div>

@if($firstCategories->isNotEmpty() || $secondCategories->isNotEmpty())
    <div class="section-1">
        <div class="container py-4">
            <div class="row">
                <!-- Khối bên trái -->
                <div class="col-md-6  mb-3 mb-md-0">
                    <div class="bg-white rounded-20 p-3 shadow-sm">
                        <div class="row">
                            <!-- 8 phần tử -->
                            @foreach($firstCategories as $category)
                                <div class="col-3 text-center">
                                    <a href="{{ $category->url }}" class="text-dark text-decoration-none ">
                                        <div class="zoom-hover">

                                            <img
                                                src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
                                                class="img-fluid rounded" width="100" height="100"
                                                alt="{{ $category->name }}">
                                            <p class="mt-2 fw-600 fs-14">{{ $category->name }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Khối bên phải -->
                <div class="col-md-6  ">
                    <div class="bg-white rounded-20 p-3 shadow-sm ">
                        <!-- 8 phần tử -->
                        <div class="row">
                            @foreach($secondCategories as $category)

                                <div class="col-3 text-center">
                                    <a href="{{ $category->name=="Review"?route("public.reviews"):$category->url }}"
                                       class="text-dark text-decoration-none ">
                                        <div class="zoom-hover">
                                            <img
                                                src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
                                                class="img-fluid rounded" width="100" height="100"
                                                alt="{{ $category->name }}">
                                            <p class="mt-2 fw-600 fs-14">{{ $category->name }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Mobile-only services filter pills band — design polish, no DB content --}}
<div class="mobile-services-filter desktop-hidden">
    <div class="container">
        <h3 class="mb-2 fw-bold">Dịch vụ</h3>
        <div class="services-pills">
            <a class="text-decoration-none badge rounded-pill new-badge active">Tất cả</a>
            <a class="text-decoration-none badge rounded-pill new-badge">Giặt sấy</a>
            <a class="text-decoration-none badge rounded-pill new-badge">Siêu thị</a>
            <a class="text-decoration-none badge rounded-pill new-badge">Đặt tiệc</a>
            <a class="text-decoration-none badge rounded-pill new-badge">Dịch vụ ch...</a>
        </div>
    </div>
</div>
