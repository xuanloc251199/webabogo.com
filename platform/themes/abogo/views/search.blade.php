<div class="blog-page container">
    <h1>{{ $title }}</h1>
    @foreach($posts as $post)
        <div class="blog-item rounded-20 bg-white position-relative">

            <a href="{{ $post->url }}" class="detail text-decoration-none text-dark">
            <h2 class="fw-500 fs-20">{{ $post->name }}</h2>
            <div class="blog-item-desc fw-400 fs-16 max-3-lines">
                {{ $post->description }}
            </div>
            <div class="blog-item-image w-100 mt-2">
                <img src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="blog-image" class="w-100 h-100 rounded-10">
            </div>
{{--            <div class="text-end mt-3">--}}
{{--                <a href="{{ $post->url }}" class="detail"><i--}}
{{--                        class="fa-solid fa-circle-plus text-muted fs-24"></i></a>--}}
{{--            </div>--}}
            </a>
        </div>
    @endforeach
</div>
