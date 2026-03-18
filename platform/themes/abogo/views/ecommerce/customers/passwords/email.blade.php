@php
    Theme::layout('blank');
@endphp

<section class="vh-100">
    <div class="container py-5 h-100 forgot-password">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-12 col-md-6">
                <div class="card shadow-2-strong rounded-20">
                    <div class="card-body">
                        <form action="{{ route('customer.password.request') }}" method="post" class="text-center">
                            @csrf
                            <h3 class="mb-5 site-logo fs-41 fw-500 text-dark">abogo</h3>
                            <div class="form-outline mb-4 text-start">
                                <label class="form-label ps-3" for="typeEmailX-2">Chúng tôi sẽ gửi mã đặt mật khẩu cho
                                    bạn qua email hoặc số điện thoại</label>
                                <input type="email" id="typeEmailX-2" name="email" class="form-control form-control-lg"
                                       placeholder="Email hoặc số điện thoại"/>
                            </div>

                            <button class="btn btn-primary btn-main-color btn-lg w-100 mb-3" type="submit">Reset</button>
                            <a href="{{ BaseHelper::getHomepageUrl() }}" class="text-decoration-none">Về trang chủ</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
