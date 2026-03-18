<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            <a class="navbar-brand me-3 site-logo fs-41 fw-500 text-dark" href="/">
                abogo
            </a>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-unstyled">
            @php $menu_nodes->loadMissing('metadata'); @endphp
            @foreach ($menu_nodes as $key => $row)
                <li>
                    <a
                        href="{{ url($row->url) }}"
                        class="d-block py-2 @if ($row->active) active @endif text-decoration-none text-dark fw-600 fs-16"
                        target="{{ $row->target }}"
                    >{{ $row->title }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
{!!
    Menu::renderMenuLocation('menu-mobile', [
        'options' => [],
        'theme' => true,
        'view' => 'footer-menu',
    ])
!!}
