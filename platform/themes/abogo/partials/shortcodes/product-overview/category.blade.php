<div class="product-category container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="swiper category-swiper bg-white rounded-20 p-3">
                <div class="swiper-wrapper">
                    @foreach($categories as $category)
                        <div class="swiper-slide p-1 p-md-0">
                            <div class="text-center">
                                <a href="{{ $category->url }}" class="text-dark text-decoration-none">
                                    <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}" class="img-fluid rounded" width="100" height="100"
                                         alt="{{ $category->name }}">
                                    <p class="my-1 fw-600 fs-14">{{ $category->name }}</p>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
