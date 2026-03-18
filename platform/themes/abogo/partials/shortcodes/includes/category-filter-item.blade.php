@foreach($posts as $post)
    <div class="swiper-slide p-1 p-md-0">
        <div class="blog-item bg-white rounded-20 shadow-sm">
            <a href="{{ $post->url }}">
                <img src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}" class="img-fluid top-rounded-20"
                     alt="{{ $post->name }}">
            </a>

            <div class="p-3">
                <span class="fs-12 fw-500 text-muted" style="
    height: 24px;
    display: block;
">{{ $post->first_category?->name }}</span>
                <h5 class="fw-600 fs-16 mt-2">
                    <a href="{{ $post->url }}" class="text-decoration-none text-dark max-2-lines">{{ $post->name }}</a>
                </h5>
                <p class="text-muted mb-0 fs-12 fw-500"><i class="fa-regular fa-calendar"></i>
                    {{ $post->created_at->translatedFormat('d/m/Y') }}</p>
            </div>
        </div>
    </div>
@endforeach
