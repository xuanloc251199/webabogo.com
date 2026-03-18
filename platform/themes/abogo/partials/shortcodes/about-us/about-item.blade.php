<div class="col-12 col-md-6 mb-3">
    <div class="about-item rounded-20 bg-white position-relative">
        <div class="text-center">
            <div class="icon">
                <img
                    src="{{ RvMedia::getImageUrl($shortcode->icon_image, null, false, RvMedia::getDefaultImage()) }}"
                    alt="icon">
            </div>
            <h2 class="fw-600 fs-22 pt-2 pb-1">{{ $shortcode->title }}</h2>
            <div class="content fw-400 fs-16 px-3 max-3-lines">
                {{ $shortcode->desc }}
            </div>
            <div class="image w-100">
                <img
                    src="{{ RvMedia::getImageUrl($shortcode->main_image, null, false, RvMedia::getDefaultImage()) }}"
                    alt="icon" class="w-100">
            </div>
        </div>
        <div class="see-more">
            <a href="{{ $shortcode->link ?? '#' }}" class="detail position-absolute bottom-0 end-0 pb-3 pe-3"><i
                    class="fa-solid fa-circle-plus text-muted fs-24"></i></a>
        </div>
    </div>
</div>
