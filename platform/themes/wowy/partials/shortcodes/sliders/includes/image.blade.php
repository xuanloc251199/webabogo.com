@if ($slider->link)
    <a href="{{ url($slider->link) }}" class="d-block m-auto">
        {{ RvMedia::image($slider->image, 'image', attributes: ['class' => 'animated', 'loading' => 'eager']) }}
    </a>
@else
    {{ RvMedia::image($slider->image, 'image', attributes: ['class' => 'animated', 'loading' => 'eager']) }}
@endif
