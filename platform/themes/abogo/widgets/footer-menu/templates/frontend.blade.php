<div class="col-6 col-md-3 footer-widget">
    <ul class="list-unstyled">
        @foreach ($items as $item)
            <li>
                <a
                    href="{{ url((string) $item->url) }}"
                    title="{{ $item->label }}"
                    {!! $item->attributes ? BaseHelper::clean($item->attributes) : null !!}
                >
                    <i class="{{ $item->icon ?? "" }} fa-fw me-2 footer-icon-color"></i> {{ $item->label }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
