<div class="section-6">
    <!-- Khối danh mục địa điểm -->
    <div class="container pt-5">
        <h3 class="mb-4 fw-bol">Danh mục địa điểm</h3>

        <!-- Swiper -->
        <div class="swiper place-swiper mobile-hidden">
            <div class="swiper-wrapper">
                @foreach($places as $place)
                    <div class="swiper-slide text-center">
                        <div class="place-item bg-white rounded-20">
                            <a href="{{ $place->url }}" class="text-dark text-decoration-none">
                                <img src="{{ RvMedia::getImageUrl($place->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $place->name }}"
                                     class="img-fluid top-rounded-20 w-100" height="230">
                                <h5 class="py-3 fw-600 fs-24">{{ $place->name }}</h5>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="desktop-hidden">
            <div class="row">
                @foreach($places->take(8) as $place)
                    <div class="col-6 mb-3">
                        <div class="place-item bg-white rounded-20 text-center">
                            <a href="{{ route('public.search',['places[]' => $place->id]) }}" class="text-dark text-decoration-none">
                                <img src="{{ RvMedia::getImageUrl($place->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $place->name }}"
                                     class="img-fluid top-rounded-20 w-100">
                                <h5 class="py-3 fw-600 fs-24">{{ $place->name }}</h5>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
