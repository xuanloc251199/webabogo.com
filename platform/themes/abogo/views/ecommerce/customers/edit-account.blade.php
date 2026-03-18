@php
    $customer = auth('customer')->user();
@endphp
<div class="account-detail-page container">
    <div class="rounded-20 bg-white p-4">
        <h1 class="text-start">Quản lý tài khoản</h1>

        <div class="account-info rounded-20 mb-4">
            <div class="row">
                <div class="col-3">
                    <div class="account-avatar text-center position-relative">
                        {{ RvMedia::image($customer->avatar_url, $customer->name, attributes: ['class' => 'bb-customer-profile-avatar-img avatar-img', 'data-bb-value' => 'customer-avatar']) }}
                        <label class="change-pic-label">
                            <img src="{{ Theme::asset()->url('images/svg/camera.svg') }}" alt="camera">
                            <input type="file" id="avatar-input" name="avatar" data-url="{{ route('customer.avatar') }}"
                                   data-bb-toggle="change-customer-avatar">
                            <span class="text-desc text-dark">Thay đổi ảnh bìa</span>
                        </label>
                    </div>
                </div>
                <div class="col-9">
                    <h2>
                        <img src="{{ Theme::asset()->url('images/svg/profile.svg') }}" alt="profile">
                        Thông tin cá nhân
                    </h2>
                    <div class="row">
                        <div class="col-2">
                            <p>Họ và tên</p>
                            <p>Email</p>
                            <p>Địa chỉ</p>
                        </div>
                        <div class="col-10">
                            <p>{{ $customer->name }}</p>
                            <p>{{ $customer->email }}</p>
                            <p>{{ $customer->addresses->first()?->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="account-info rounded-20">
            <h2>Đổi mật khẩu</h2>
            <form action="{{ route('customer.post.change-password') }}" method="post">
                @csrf
                <div class="mb-3 row">
                    <label for="current-pass" class="col-sm-2 col-form-label">Mật khẩu hiện tại</label>
                    <div class="col-sm-3">
                        <input type="password" class="form-control" id="current-pass" name="old_password" value="">
                        @error("old_password")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Mật khẩu mới</label>
                    <div class="col-sm-3">
                        <input type="password" class="form-control" name="password" id="inputPassword">
                        @error("password")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="reInputPassword" class="col-sm-2 col-form-label">Nhập lại mật khẩu mới</label>
                    <div class="col-sm-3">
                        <input type="password" class="form-control" id="reInputPassword" name="password_confirmation">
                        @error("password_confirmation")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>

                <div class="text-start">
                    <button type="submit" class="btn btn-primary rounded-16 bg-base-black">Đổi mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    @if (Session::has('success_msg'))
    const successMessage = '{{ session('success_msg') }}';
    @endif
    @if (Session::has('error_msg'))
    const errorMessage = '{{ session('error_msg') }}';
    @endif

</script>
