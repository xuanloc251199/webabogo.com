@php
    Theme::layout('blank');
@endphp

<section class="vh-100">
    <div class="container py-5 h-100 register">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-12 col-md-6">
                <div class="card shadow-2-strong rounded-20">
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="mb-5 site-logo fs-41 fw-500 text-dark">abogo</h3>
                        </div>
                        <form action="{{ route('customer.register.post') }}" method="post">
                            @csrf
                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="typeNameX-2">Họ và tên</label>
                                <input type="text" id="typeNameX-2" name="name" class="form-control form-control-lg"
                                       placeholder="Nhập Tên"/>
                            </div>
                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="typeEmailX-2">Tài khoản</label>
                                <input type="email" id="typeEmailX-2" name="email" class="form-control form-control-lg"
                                       placeholder="Nhập email hoặc số điện thoại"/>
                            </div>

                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="typePasswordX-2">Mật khẩu</label>
                                <input type="password" id="typePasswordX-2" name="password" class="form-control form-control-lg"
                                       placeholder="Nhập mật khẩu"/>
                            </div>

                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="reTypePasswordX-2">Nhập lại mật khẩu</label>
                                <input type="password" id="reTypePasswordX-2" name="password_confirmation" class="form-control form-control-lg"
                                       placeholder="Nhập lại mật khẩu"/>
                            </div>

                            <button class="btn btn-primary btn-main-color btn-lg w-100" type="submit">Đăng ký</button>
                        </form>

                        <div class="divider">
                            <span>Hoặc</span>
                        </div>
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
                            Bạn đã có tài khoản? &nbsp; <a href="{{ route('customer.login') }}" class="text-decoration-none">Đăng nhập ngay</a>
                            &nbsp; <a class="back-to-home text-decoration-none" href="{{ BaseHelper::getHomepageUrl() }}" class="text-decoration-none">Về trang chủ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
