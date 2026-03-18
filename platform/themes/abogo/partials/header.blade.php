<?php
$placesChoice = \Botble\Place\Models\Place::where("status", \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)->get();
$notifs = [];
if (auth("customer")->check()) {
    $notifications = \Botble\Ecommerce\Models\Notification::query()
        ->whereNull("customer_id")
        ->orWhere(function ($q) {
            $q->where("customer_id", auth("customer")->id());
        })->orderBy("created_at", "desc")->get();
} else {
    $notifications = \Botble\Ecommerce\Models\Notification::query()
        ->whereNull("customer_id")
        ->orderBy("created_at", "desc")->get();
}

foreach ($notifications as $notification) {
    $notifs[] = [
        "title" => $notification->title,
        "message" => $notification->content,
        "time" => $notification->created_at->diffForHumans(),
    ];
}

?>
    <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
          name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! Theme::header() !!}

    <style>
        /* Modal styles */
        .notification-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .notification-modal-content {
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 400px;
            max-height: 80vh;
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
        }

        main {
            margin-top: 90px;
        }

        header > nav.navbar {
            position: fixed;
            width: 100%;
            z-index: 999;
            top: 0;

        }

        .notification-modal-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .notification-modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
        }

        .btn-close:before {
            content: "×";
            font-weight: bold;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa !important;
        }

        span.total-product-cart {
            position: absolute;
            right: -10px;
            top: -15px;
            color: #fff;
            background-color: red;
            padding: 2px 7px;
            font-size: 11px;
            border-radius: 10px;
        }

        .ck-content img {
            border-radius: 20px;
        }
    </style>
</head>
<body @if (BaseHelper::isRtlEnabled()) dir="rtl" @endif>
{!! apply_filters(THEME_FRONT_BODY, null) !!}
<header>
    <nav class="navbar navbar-expand-lg bg-light shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Bên trái -->
            <div class="d-flex align-items-center search-form">
                <!-- Logo -->
                <a class="navbar-brand me-3 site-logo fs-41 fw-500 text-dark" href="/">
                    abogo
                </a>

                <!-- Ô chọn địa điểm -->
                <select class="form-select select2-location bg-gray border-0" id="place_search">
                    <option value="">Địa điểm</option>
                    @foreach($placesChoice as $p)
                        <option value="{{$p->id}}"
                                {{request()->url() ==$p->url?"selected":""}} data-url="{{$p->url}}">{{$p->name}}</option>
                    @endforeach
                </select>

                <div class="input-group ms-2 search-keyword-form">
                    <span class="input-group-text bg-gray border-0">
                        <i class="fas fa-search text-gray"></i>
                    </span>
                    <input type="text"
                           class="form-control bg-gray border-0"
                           placeholder="Tìm kiếm"
                           id="searchInput"
                           autocomplete="off">
                    <div class="search-dropdown bg-white shadow-sm rounded-bottom d-none"
                         id="searchDropdown">
                        <div class="p-2" id="searchResults">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bên phải -->
            <div class="d-flex align-items-center right-menu">
                <!-- Chọn ngôn ngữ -->
                @if (is_plugin_active('language'))
                    {!! Theme::partial('language-switcher') !!}
                @endif
                <!-- Icon giỏ hàng -->
                <a href="{{auth('customer')->check()?route("public.cart"):route("customer.login")}}"
                   class="me-3 position-relative pt-1">
                    <i class="fa-solid fa-cart-plus text-dark fs-22"></i>
                    {{--                    @if(Botble\Ecommerce\Facades\Cart::instance('cart')->rawTotalQuantity()>0)--}}
                    <span
                        class="total-product-cart">{{Botble\Ecommerce\Facades\Cart::instance('cart')->rawTotalQuantity()}}</span>
                    {{--                    @endif--}}
                </a>

                <!-- Icon thông báo -->
                <div class="position-relative">
                    <a href="#" class="me-3 position-relative pt-1" id="notificationIcon">
                        <i class="fa-solid fa-bell text-dark fs-22"></i>
                    </a>
                </div>

                <!-- Modal thông báo -->
                <div class="notification-modal d-none" id="notificationModal">
                    <div class="notification-modal-overlay"></div>
                    <div class="notification-modal-content">
                        <div class="notification-modal-header">
                            <h5 class="mb-0">Thông báo</h5>
                            <button type="button" class="btn-close" id="closeNotificationModal"></button>
                        </div>
                        <div class="notification-modal-body" id="notificationResults">
                        </div>
                    </div>
                </div>

                <!-- Icon menu (Hiện navbar khi ấn) -->
                <button class="btn mobile-hidden" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasMenu">
                    <img src="{{ Theme::asset()->url('images/svg/menu.svg') }}" alt="menu-icon">
                </button>

                <!-- Offcanvas Menu -->
                {!!
                    Menu::renderMenuLocation('main-menu', [
                        'view' => 'main-menu',
                    ])
                !!}

                <!-- Nút Đăng nhập / Đăng ký -->
                <div class="ms-3 d-none d-md-block">
                    @if (auth('customer')->check())
                        <a href="{{ route('customer.overview') }}"
                           class="fw-600 fs-14 text-dark text-decoration-none">{{ auth('customer')->user()->name }}</a>
                    @else
                        <a href="{{ route('customer.login') }}" class="fw-600 fs-14 text-dark text-decoration-none">Đăng
                            nhập</a>
                        <span>/</span>
                        <a href="{{ route('customer.register') }}" class="fw-600 fs-14 text-dark text-decoration-none">Đăng
                            ký</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const searchDropdown = document.getElementById('searchDropdown');
            const searchResults = document.getElementById('searchResults');
            const placeSearch = document.getElementById('place_search');

            let searchTimeout;

            const sampleData = [
                {title: 'Khách sạn Hà Nội', description: 'Khách sạn 5 sao tại trung tâm'},
                {title: 'Nhà hàng Sài Gòn', description: 'Ẩm thực đường phố'},
                {title: 'Du lịch Đà Nẵng', description: 'Tour biển đảo'},
                {title: 'Homestay Đà Lạt', description: 'Nghỉ dưỡng view đồi thông'}
            ];

            function fakeAjaxSearch(query, place) {
                return new Promise((resolve) => {
                    setTimeout(() => {
                        $.ajax({
                            url: "{{route('public.ajax.suggest.search')}}",
                            method: 'GET',
                            data: {q: query, place: place},
                            success: function (response) {
                                $("#searchResults").html(response);

                            },
                            error: function (xhr, status, error) {
                                reject(error);
                            }
                        });
                    }, 300);
                });
            }

            function displayResults(results) {
                searchResults.appendChild(results);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                if (this.value.length > 0) {
                    searchDropdown.classList.remove('d-none');
                    searchTimeout = setTimeout(async () => {
                        const results = await fakeAjaxSearch(this.value, placeSearch.value);

                        // searchResults.appendChild(results);
                        // displayResults(results);
                    }, 300);
                } else {
                    searchDropdown.classList.add('d-none');
                }
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.classList.add('d-none');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationProfile = document.getElementById('notificationProfile');
            const notificationModal = document.getElementById('notificationModal');
            const notificationResults = document.getElementById('notificationResults');
            const closeNotificationModal = document.getElementById('closeNotificationModal');
            const notificationOverlay = document.querySelector('.notification-modal-overlay');

            const sampleNotifications = {!! json_encode($notifs) !!}
            // const sampleNotifications = [
            //     {
            //         title: 'Đặt phòng thành công',
            //         message: 'Bạn đã đặt phòng thành công tại Khách sạn ABC',
            //         time: '5 phút trước',
            //         isRead: false
            //     },
            //     {
            //         title: 'Giảm giá đặc biệt',
            //         message: 'Ưu đãi 30% cho đặt phòng trong tuần này',
            //         time: '1 giờ trước',
            //         isRead: true
            //     },
            //     {
            //         title: 'Xác nhận thanh toán',
            //         message: 'Thanh toán đặt phòng #123 thành công',
            //         time: '2 giờ trước',
            //         isRead: true
            //     }
            // ];

            function fakeAjaxNotifications() {
                return new Promise((resolve) => {
                    setTimeout(() => {
                        resolve(sampleNotifications);
                    }, 300);
                });
            }

            function displayNotifications(notifications) {
                notificationResults.innerHTML = '';
                if (notifications.length === 0) {
                    notificationResults.innerHTML = '<div class="p-2 text-center">Không có thông báo mới</div>';
                    return;
                }

                notifications.forEach(notification => {
                    const div = document.createElement('div');
                    div.className = `notification-item p-2 border-bottom ${notification.isRead ? 'bg-white' : 'bg-light'}`;
                    div.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-500">${notification.title}</div>
                        <small class="text-muted">${notification.message}</small>
                        <div class="text-muted" style="font-size: 0.75rem;">${notification.time}</div>
                    </div>
                    ${!notification.isRead ? '<div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>' : ''}
                </div>
            `;
                    notificationResults.appendChild(div);
                });

                const viewAllDiv = document.createElement('div');
                viewAllDiv.className = 'text-center p-2 border-top';
                // viewAllDiv.innerHTML = '<a href="#" class="text-primary text-decoration-none">Xem tất cả thông báo</a>';
                notificationResults.appendChild(viewAllDiv);
            }

            // Hàm mở modal
            function openNotificationModal() {
                notificationModal.classList.remove('d-none');
                document.body.style.overflow = 'hidden'; // Ngăn scroll body khi modal mở
            }

            // Hàm đóng modal
            function closeModal() {
                notificationModal.classList.add('d-none');
                document.body.style.overflow = ''; // Khôi phục scroll body
            }

            // Click vào icon thông báo
            notificationIcon.addEventListener('click', async function (e) {
                e.preventDefault();
                const notifications = await fakeAjaxNotifications();
                displayNotifications(notifications);
                openNotificationModal();
            });

            // Click vào nút đóng modal
            closeNotificationModal.addEventListener('click', function () {
                closeModal();
            });

            // Click vào overlay để đóng modal
            notificationOverlay.addEventListener('click', function () {
                closeModal();
            });

            // Đóng modal khi nhấn ESC
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !notificationModal.classList.contains('d-none')) {
                    closeModal();
                }
            });
        });
    </script>
</header>
