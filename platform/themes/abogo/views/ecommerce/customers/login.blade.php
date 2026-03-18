@php
    Theme::layout('blank');
@endphp

<section class="vh-100">
    <div class="container py-5 h-100 login">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                <div class="card rounded-20">
                    <div class="card-body">
                        <form action="{{ route('customer.login.post') }}" method="post">
                            @csrf
                            <div class="text-center">
                                <h3 class="mb-5 site-logo fs-41 fw-500 text-dark">abogo</h3>
                            </div>
                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="typeEmailX-2">Tài khoản</label>
                                <input type="email" id="typeEmailX-2" name="email" class="form-control form-control-lg"
                                       placeholder="Nhập email hoặc số điện thoại"/>
                            </div>

                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="typePasswordX-2">Mật khẩu</label>
                                <input type="password" id="typePasswordX-2" name="password"
                                       class="form-control form-control-lg"
                                       placeholder="Nhập mật khẩu"/>
                            </div>

                            <div class="d-flex justify-content-between align-items-center my-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="remember"
                                           id="flexSwitchCheckDefault"/>
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Ghi nhớ</label>
                                </div>
                                <a href="{{ route('customer.password.reset') }}"
                                   class="text-decoration-none text-primary ư">Quên mật
                                    khẩu?</a>
                            </div>

                            <button class="btn btn-primary btn-main-color btn-lg w-100" type="submit">Login</button>

                            <div class="divider">
                                <span>Hoặc</span>
                            </div>

                            {{--                            <div class="d-flex justify-content-center gap-3">--}}
                            {{--                                <a class="btn btn-outline-danger social-btn" href="{{route("customer.login.with.gg")}}">--}}
                            {{--                                    <i class="fab fa-google"></i>--}}
                            {{--                                </a>--}}
                            {{--                            </div>--}}
                            <div class="d-flex justify-content-center gap-3">
                                @foreach (SocialService::getProviderKeys() as $item)
                                    @if (SocialService::getProviderEnabled($item))
                                        {!! apply_filters('social_login_' . $item . '_render', sprintf('

                                                    <a
                                                        class="%s btn btn-outline-danger social-btn"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="%s"
                                                        href="%s"
                                                    ><i class="fab fa-google"></i>
                                                    </a>

                                        ', $item, $item, route('auth.social', isset($params) ? array_merge([$item], $params) : $item)), $item) !!}
                                    @endif
                                @endforeach
                            </div>
                            <div class="text-center my-3">
                                Bạn chưa có tài khoản? &nbsp;<a href="{{ route('customer.register') }}"
                                                                class="text-decoration-none">Đăng ký ngay</a>
                                &nbsp; <a class="back-to-home text-decoration-none"
                                          href="{{ BaseHelper::getHomepageUrl() }}" class="text-decoration-none">Về
                                    trang chủ</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
