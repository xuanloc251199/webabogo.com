<div class="mobile-menu desktop-hidden" {!! $options !!}>
    @foreach ($menu_nodes as $key => $row)
        <a
            href="{{ url($row->url) }}"
            class="menu-item @if ($row->active) active @endif"
            target="{{ $row->target }}"
        >
            @if ($row->icon_font)
                <i class='{{ trim($row->icon_font) }}'></i>
            @endif
            {{ $row->title }}
        </a>
    @endforeach
</div>
