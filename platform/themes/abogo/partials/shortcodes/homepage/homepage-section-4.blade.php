<div class="section-4">
    <!-- Khối Tin tức -->
    <div class="container py-4">
        <h3 class="mb-3 fw-bold">Tin tức mới nhất</h3>

        <!-- Bộ lọc danh mục -->

        <div class="mb-3">
            <div class="swiper category-new-swiper" style="display: none">
                <div class="swiper-wrapper">

                    <div class="swiper-slide p-1 p-md-0">
                        <a href="javascript:void(0);"
                           class="text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge category-filter-item active"
                           data-id=""
                           data-url="{{ route('public.ajax.filter-category', ['category_id' => 'all']) }}"
                        >
                            Tất cả
                        </a>
                    </div>
                    @foreach($categories as $category)
                        <div class="swiper-slide p-1 p-md-0">
                            <a class="text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge category-filter-item"
                               data-id="{{ $category->id }}"
                               data-url="{{ route('public.ajax.filter-category', ['category_id' => $category->id]) }}"
                            >
                                {{ $category->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mb-3 category-swiper-desk">
            <a href="javascript:void(0);"
               class="text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge category-filter-item active"
               data-id=""
               data-url="{{ route('public.ajax.filter-category', ['category_id' => 'all']) }}"
            >
                Tất cả
            </a>
            @foreach($categories as $category)
                <a class="text-decoration-none badge rounded-pill bg-neutral-2 fs-16 fw-400 new-badge category-filter-item"
                   data-id="{{ $category->id }}"
                   data-url="{{ route('public.ajax.filter-category', ['category_id' => $category->id]) }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <!-- Swiper Tin tức -->
        <div class="swiper blog-swiper">
            <div class="swiper-wrapper category-filter-content">
                @include(Theme::getThemeNamespace('partials.shortcodes.includes.category-filter-item'),['posts' => get_recent_posts(8)])
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="/blog" class="btn btn-main-color px-5">Xem Tất Cả</a>
        </div>
    </div>
</div>
