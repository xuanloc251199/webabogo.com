<div class="blog-item rounded-20 bg-white position-relative">
    <h2 class="fw-500 fs-20">{{ $blog->name }}</h2>
    <div class="blog-item-desc fw-400 fs-16 max-3-lines">
        {!! BaseHelper::clean($blog->description) !!}
    </div>
    <div class="blog-item-image w-100 mt-2">
        <img src="{{ RvMedia::getImageUrl($blog->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $blog->name }}"
             class="w-100 h-100 rounded-10">
    </div>
    <div class="text-end mt-3">
        <a href="{{ $blog->url }}" class="detail"><i
                class="fa-solid fa-circle-plus text-muted fs-20"></i></a>
    </div>

</div>
