<div class="blog-page-detail container">
    <h1>{{ $post->name }}</h1>
    <div class="blog-date mb-3">
        <i class="fa-solid fa-calendar-days me-3"></i> {{ $post->created_at->translatedFormat('d/m/Y') }}
    </div>
    <div class="blog-content rounded-20 bg-white">
        <div class="ck-content">
            {!! BaseHelper::clean($post->content) !!}
        </div>
    </div>
</div>
<style>
    .ck-content * {
        overflow: auto!important;
    }
</style>