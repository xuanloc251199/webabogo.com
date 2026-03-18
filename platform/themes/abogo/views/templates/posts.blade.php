
@if ($posts->isNotEmpty())
    <h1 class="d-none">{{ SeoHelper::getTitle() }}</h1>
    <div class="container">
        <div class="search-section ">
            <form action="{{ route('public.search') }}" class="search-wrapper d-flex flex-wrap">
                <!-- Input tìm kiếm -->
                <div class="input-group search-input">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="q" placeholder="Tìm kiếm...">
                </div>

                <!-- Select2 Danh mục -->
                <div class="select-group">
                    <div class="select-wrapper">
                        <select class="form-select select-category" name="categories[]">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select2 Địa điểm -->
                    <div class="select-wrapper">
                        <select class="form-select select-location" name="places[]">
                            @foreach($places as $place)
                                <option value="{{ $place->id }}">{{ $place->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <!-- Nút tìm kiếm -->
                <button type="submit" class="btn btn-main-color search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="container">
        <div class="blog-category-section">
            <div class="swiper blog-category-swiper bg-white rounded-20 mobile-hidden">
                <div class="swiper-wrapper">
                    @foreach($categories as $category)
                        <div class="swiper-slide">
                            <a href="{{ route('public.search',['categories[]' => $category->id]) }}" class="text-dark text-decoration-none">
                                <div class="category-item">
                                    <div class="category-image">
                                        <i class="{{ $category->icon ?? 'fa-solid fa-hotel' }}"></i>
                                    </div>
                                    <p>{{ $category->name }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="desktop-hidden container p-0">
                <div class="bg-white rounded-20 px-3 py-2">
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-3">
                                <div class="category-item">
                                    <a href="{{ route('public.search',['categories[]' => $category->id]) }}" class="text-dark text-decoration-none">
                                        <div class="category-image">
                                            <i class="{{ $category->icon ?? 'fa-solid fa-hotel' }}"></i>
                                        </div>
                                        <p>{{ $category->name }}</p>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="recent-blog container mt-2">
        <div class="text-center">
            <div class="icon">
                <img src="{{ Theme::asset()->url('images/svg/blog.svg') }}" alt="icon">
            </div>
            <h2>Bài viết Xem nhiều nhất</h2>
        </div>
        <div class="mobile-hidden">
            <div class="swiper blog-swiper mb-3">
                <div class="swiper-wrapper">
                    <!-- Bắt đầu blog-item -->
                    @foreach($featuredBlogs as $key => $blog)
                        <div class="swiper-slide">
                            <div class="blog-item bg-white rounded-20 shadow-sm">
                                <a href="{{ $blog->url }}">
                                    <img src="{{ RvMedia::getImageUrl($blog->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                                         alt="Tiêu đề bài viết">
                                </a>

                                <div class="p-3">
                                    <span class="fs-12 fw-500 text-muted">{{ $blog->first_category?->name }}</span>
                                    <a href="{{ $blog->url }}" class="fw-600 fs-16 text-dark mt-2 max-2-lines text-decoration-none">
                                        {{ $blog->name }}
                                    </a>
                                    <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                                        {{ $blog->created_at->translatedFormat('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- Kết thúc blog-item -->
                </div>
            </div>
        </div>
        <!-- Swiper Tin tức -->
        <div class="desktop-hidden">
            <div class="swiper blog-swiper mb-3">
                <div class="swiper-wrapper">

                    <!-- Bắt đầu blog-item -->
                    @foreach($featuredBlogs as $key => $blog)
                        @if($key % 2 == 0)
                            <div class="swiper-slide">
                                <div class="blog-item bg-white rounded-20 shadow-sm">
                                    <a href="{{ $blog->url }}">
                                        <img src="{{ RvMedia::getImageUrl($blog->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                                             alt="Tiêu đề bài viết">
                                    </a>
                                    <div class="p-3">
                                        <span class="fs-12 fw-500 text-muted">{{ $blog->first_category?->name }}</span>
                                        <a href="{{ $blog->url }}" class="fw-600 fs-16 text-dark mt-2 max-2-lines text-decoration-none">
                                            {{ $blog->name }}
                                        </a>
                                        <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                                            {{ $blog->created_at->translatedFormat('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <!-- Kết thúc blog-item -->
                </div>
            </div>
            <div class="swiper blog-swiper desktop-hidden">
                <div class="swiper-wrapper">
                    <!-- Bắt đầu blog-item -->
                    @foreach($featuredBlogs as $key => $blog)
                        @if($key % 2 != 0)
                            <div class="swiper-slide">
                                <div class="blog-item bg-white rounded-20 shadow-sm">
                                    <a href="{{ $blog->url }}">
                                        <img src="{{ RvMedia::getImageUrl($blog->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                                             alt="Tiêu đề bài viết">
                                    </a>
                                    <div class="p-3">
                                        <span class="fs-12 fw-500 text-muted">{{ $blog->first_category?->name }}</span>
                                        <a href="{{ $blog->url }}" class="fw-600 fs-16 text-dark mt-2 max-2-lines text-decoration-none">
                                            {{ $blog->name }}
                                        </a>
                                        <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                                            {{ $blog->created_at->translatedFormat('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <!-- Kết thúc blog-item -->
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('public.search',['type' => 'featured']) }}" class="btn btn-main-color px-5">Xem Tất Cả</a>
        </div>
    </div>
    <div class="featured-blog container mt-5">
        <div class="text-center">
            <div class="icon">
                <img src="{{ Theme::asset()->url('images/svg/blog.svg') }}" alt="icon">
            </div>
            <h2>Bài viết Mới Nhất</h2>
        </div>
        <!-- Swiper Tin tức -->

        <div class="mobile-hidden">
            <div class="swiper blog-swiper mb-3">
                <div class="swiper-wrapper">
                    <!-- Bắt đầu blog-item -->
                    @foreach($recentBlogs as $key => $blog)
                        <div class="swiper-slide">
                            <div class="blog-item bg-white rounded-20 shadow-sm">
                                <a href="{{ $blog->url }}">
                                    <img src="{{ RvMedia::getImageUrl($blog->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                                         alt="Tiêu đề bài viết">
                                </a>
                                <div class="p-3">
                                    <span class="fs-12 fw-500 text-muted">{{ $blog->first_category?->name }}</span>
                                    <a href="{{ $blog->url }}" class="fw-600 fs-16 text-dark mt-2 max-2-lines text-decoration-none">
                                        {{ $blog->name }}
                                    </a>
                                    <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                                        {{ $blog->created_at->translatedFormat('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- Kết thúc blog-item -->
                </div>
            </div>
        </div>
        <!-- Swiper Tin tức -->
        <div class="desktop-hidden">
            <div class="swiper blog-swiper mb-3">
                <div class="swiper-wrapper">

                    <!-- Bắt đầu blog-item -->
                    @foreach($recentBlogs as $key => $blog)
                        @if($key % 2 == 0)
                            <div class="swiper-slide">
                                <div class="blog-item bg-white rounded-20 shadow-sm">
                                    <a href="{{ $blog->url }}">
                                        <img src="{{ RvMedia::getImageUrl($blog->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                                             alt="Tiêu đề bài viết">
                                    </a>
                                    <div class="p-3">
                                        <span class="fs-12 fw-500 text-muted">{{ $blog->first_category?->name }}</span>
                                        <a href="{{ $blog->url }}" class="fw-600 fs-16 text-dark mt-2 max-2-lines text-decoration-none">
                                            {{ $blog->name }}
                                        </a>
                                        <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                                            {{ $blog->created_at->translatedFormat('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <!-- Kết thúc blog-item -->
                </div>
            </div>
            <div class="swiper blog-swiper desktop-hidden">
                <div class="swiper-wrapper">
                    <!-- Bắt đầu blog-item -->
                    @foreach($recentBlogs as $key=>$blog)
                        @if($key % 2 != 0)
                            <div class="swiper-slide">
                                <div class="blog-item bg-white rounded-20 shadow-sm">
                                    <a href="{{ $blog->url }}">
                                        <img src="{{ RvMedia::getImageUrl($blog->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                                             alt="Tiêu đề bài viết">
                                    </a>
                                    <div class="p-3">
                                        <span class="fs-12 fw-500 text-muted">{{ $blog->first_category?->name }}</span>
                                        <a href="{{ $blog->url }}" class="fw-600 fs-16 text-dark mt-2 max-2-lines text-decoration-none">
                                            {{ $blog->name }}
                                        </a>
                                        <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                                            {{ $blog->created_at->translatedFormat('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <!-- Kết thúc blog-item -->
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('public.search',['type' => 'recent']) }}" class="btn btn-main-color px-5">Xem Tất Cả</a>
        </div>
    </div>

    <div class="blog-location-section container mt-5">
        <div class="text-center">
            <div class="icon">
                <img src="{{ Theme::asset()->url('images/svg/blog.svg') }}" alt="icon">
            </div>
            <h2>Bài viết theo địa điểm</h2>
        </div>

        <div class="rounded-20 bg-white p-4">
            <div class="swiper blog-location-swiper mobile-hidden">
                <div class="swiper-wrapper">
                    @foreach($places->chunk(2) as $chunk)
                        <div class="swiper-slide">
                            @foreach($chunk as $place)
                                <div class="location-item">
                                    <a href="{{ route('public.search',['places[]' => $place->id]) }}" class="text-dark text-decoration-none">
                                        <img src="{{ RvMedia::getImageUrl($place->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $place->name }}">
                                        <span>{{ $place->name }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="desktop-hidden">
                <div class="row">
                    @foreach($places as $item)
                        <div class="col-6">
                            <div class="location-item">
                                <a href="{{ route('public.search',['places[]' => $place->id]) }}" class="text-dark text-decoration-none">
                                    <img src="{{ RvMedia::getImageUrl($item->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="Location 1">
                                    <span>{{ $item->name }}</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
