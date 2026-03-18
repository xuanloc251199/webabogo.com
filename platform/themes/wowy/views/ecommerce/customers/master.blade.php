@php
    Theme::layout('full-width');
@endphp
<section class="pt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 m-auto">
                <div class="row">
                    <div class="col-md-4">
                        <div class="dashboard-menu">
                            <ul class="nav flex-column">
                                @foreach (DashboardMenu::getAll('customer') as $item)
                                    @continue(! $item['name'])

                                    <li class="nav-item">
                                        <a
                                            @class(['nav-link', 'active' => $item['active']])
                                            href="{{ $item['url'] }}"
                                        >
                                            <x-core::icon :name="$item['icon']" />

                                            {{ $item['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="tab-content dashboard-content">
                            <div class="tab-pane fade active show" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
