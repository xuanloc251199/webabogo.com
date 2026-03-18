<div class="cta-fixed d-flex flex-column gap-2">
    @foreach(Theme::getSocialLinks() as $item)
        <a {!! $item->getAttributes(['class' => 'cta-btn text-decoration-none']) !!}>{!! $item->getIconHtml(['height' => 50, 'class' => 'rounded-circle']) !!}</a>
    @endforeach
</div>

<footer class="pt-5">
    <div class="pb-3 pb-md-5">
        <div class="container">
            <div class="bg-white text-center rounded-20">

                <!-- Logo -->
                <a class="navbar-brand me-3 site-logo fs-41 fw-500 text-dark" href="/">
                    abogo
                </a>

                <!-- Footer Widgets -->
                <div class="row justify-content-center">
                    {!! dynamic_sidebar('footer_sidebar') !!}

                    <!-- Cột Widget 4 (Chứng nhận) -->
                    <div class="col-6 col-md-3 footer-widget">
                        <div class="d-flex justify-content-center flex-column ps-3">
                            <img src="{{ Theme::asset()->url('images/common/bo-cong-thuong.png') }}" alt="Bộ Công Thương" class="me-2" width="150" height="">
                            <img src="{{ Theme::asset()->url('images/common/DMCA.png') }}" alt="DMCA" width="150" height="55">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
{!! Theme::footer() !!}


@stack('after-footer')
@include('sweetalert::alert')
</body>
</html>
