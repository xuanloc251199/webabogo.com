<div class="account-page container">
    <!-- Tiêu đề -->
    <h1 class="text-start">Tài khoản</h1>

    <!-- Avatar -->
    <div class="account-avatar text-center">
        {{ RvMedia::image($customer->avatar_url, $customer->name, attributes: ['class' => 'bb-customer-profile-avatar-img avatar-img', 'data-bb-value' => 'customer-avatar']) }}
        <h3 class="mt-3">{{ $customer->name }} <a href="{{ route('customer.edit-account') }}" class="text-dark"><i
                    class="fa-regular fa-pen-to-square"></i></a>
        </h3>
        <p class="text-muted">{{ $customer->email }}</p>
    </div>

    <!-- Khối tính năng -->
    <div class="account-features">
        <div class="row">
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    <a href="{{ route('customer.edit-account') }}" class="text-white text-decoration-none">
                        <i class="fas fa-user"></i>
                        <span>Hồ sơ</span>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    <a href="{{ route('public.orders.history') }}" class="text-white text-decoration-none">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Đơn hàng</span>
                    </a>
                </div>
            </div>

            <!-- Mạng xã hội -->
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fab fa-youtube"></i>
                    <span>YouTube</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fab fa-tiktok"></i>
                    <span>TikTok</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fab fa-twitter"></i>
                    <span>Twitter</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fab fa-linkedin-in"></i>
                    <span>LinkedIn</span>
                </div>
            </div>
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                     style="cursor: pointer;">
                    <i class="fa-brands fa-weixin"></i>
                    <span>Wechat</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4" id="notificationProfile">
                <div class="feature-box rounded-20">
                    <i class="fas fa-bell"></i>
                    <span>Thông báo</span>
                </div>
            </div>
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    <a href="tel:{{auth("customer")->user()->phone}}" class="text-white text-decoration-none">
                        <i class="fas fa-phone"></i>
                        <span>Số điện thoại</span>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    <a href="{{ route('customer.logout') }}" class="text-white text-decoration-none">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal cập nhật mạng xã hội -->
<div class="modal fade" id="socialMediaModal" tabindex="-1" aria-labelledby="socialMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="socialMediaModalLabel">
                    <i class="fas fa-share-alt me-2"></i>Cập nhật mạng xã hội
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="socialMediaForm" action="{{ route('customer.update-social-media') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Facebook -->
                        <div class="col-md-6 mb-3">
                            <label for="facebook_url" class="form-label">
                                <i class="fab fa-facebook-f text-primary me-2"></i>Facebook
                            </label>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                                   value="{{ $customer->facebook_url ?? '' }}"
                                   placeholder="https://facebook.com/username">
                        </div>

                        <!-- Instagram -->
                        <div class="col-md-6 mb-3">
                            <label for="instagram_url" class="form-label">
                                <i class="fab fa-instagram text-danger me-2"></i>Instagram
                            </label>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                                   value="{{ $customer->instagram_url ?? '' }}"
                                   placeholder="https://instagram.com/username">
                        </div>

                        <!-- YouTube -->
                        <div class="col-md-6 mb-3">
                            <label for="youtube_url" class="form-label">
                                <i class="fab fa-youtube text-danger me-2"></i>YouTube
                            </label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url"
                                   value="{{ $customer->youtube_url ?? '' }}"
                                   placeholder="https://youtube.com/channel/...">
                        </div>

                        <!-- TikTok -->
                        <div class="col-md-6 mb-3">
                            <label for="tiktok_url" class="form-label">
                                <i class="fab fa-tiktok text-dark me-2"></i>TikTok
                            </label>
                            <input type="url" class="form-control" id="tiktok_url" name="tiktok_url"
                                   value="{{ $customer->tiktok_url ?? '' }}"
                                   placeholder="https://tiktok.com/@username">
                        </div>

                        <!-- Twitter -->
                        <div class="col-md-6 mb-3">
                            <label for="twitter_url" class="form-label">
                                <i class="fab fa-twitter text-info me-2"></i>Twitter
                            </label>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                   value="{{ $customer->twitter_url ?? '' }}"
                                   placeholder="https://twitter.com/username">
                        </div>

                        <!-- LinkedIn -->
                        <div class="col-md-6 mb-3">
                            <label for="linkedin_url" class="form-label">
                                <i class="fab fa-linkedin-in text-primary me-2"></i>LinkedIn
                            </label>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url"
                                   value="{{ $customer->linkedin_url ?? '' }}"
                                   placeholder="https://linkedin.com/in/username">
                        </div>
                        <!-- Wechat -->
                        <div class="col-md-6 mb-3">
                            <label for="linkedin_url" class="form-label">
                                <i class="fab fa-weixin text-primary me-2"></i>Wechat
                            </label>
                            <input type="url" class="form-control" id="wechat_url" name="wechat_url"
                                   value="{{ $customer->wechat_url ?? '' }}"
                                   placeholder="https://www.wechat.com/">
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Vui lòng nhập đầy đủ URL bao gồm https://
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const socialMediaForm = document.getElementById('socialMediaForm');
        const modal = document.getElementById('socialMediaModal');

        // Xử lý submit form
        socialMediaForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Hiển thị loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error === false) {
                        // Thành công
                        showAlert('success', 'Cập nhật mạng xã hội thành công!');

                        // Đóng modal sau 1.5 giây
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(modal).hide();
                        }, 1500);
                    } else {
                        // Lỗi
                        showAlert('error', data.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Có lỗi xảy ra, vui lòng thử lại!');
                })
                .finally(() => {
                    // Khôi phục button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Hàm hiển thị thông báo
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

            const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

            // Thêm alert vào modal body
            const modalBody = modal.querySelector('.modal-body');
            const existingAlert = modalBody.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            modalBody.insertAdjacentHTML('afterbegin', alertHtml);
        }

        // Validation URL
        const urlInputs = modal.querySelectorAll('input[type="url"]');
        urlInputs.forEach(input => {
            input.addEventListener('blur', function () {
                if (this.value && !isValidUrl(this.value)) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'Vui lòng nhập URL hợp lệ (bao gồm https://)');
                } else {
                    this.classList.remove('is-invalid');
                    removeFieldError(this);
                }
            });
        });

        function isValidUrl(string) {
            try {
                new URL(string);
                return string.startsWith('http://') || string.startsWith('https://');
            } catch (_) {
                return false;
            }
        }

        function showFieldError(field, message) {
            removeFieldError(field);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        function removeFieldError(field) {
            const errorDiv = field.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    });
</script>

<style>
    /* Styling cho các icon mạng xã hội - tất cả màu trắng */
    .feature-box i.fab {
        font-size: 1.5rem;
        color: white !important;
    }

    .feature-box i.fab.fa-facebook-f,
    .feature-box i.fab.fa-instagram,
    .feature-box i.fab.fa-youtube,
    .feature-box i.fab.fa-tiktok,
    .feature-box i.fab.fa-twitter,
    .feature-box i.fab.fa-linkedin-in {
        color: white !important;
        background: none !important;
        -webkit-background-clip: unset !important;
        -webkit-text-fill-color: white !important;
        background-clip: unset !important;
    }

    /* Hover effects */
    .feature-box:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Modal styling */
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    .modal-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
    }

    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem 1.25rem;
    }

    .alert-success {
        background-color: #d1edff;
        color: #0c5460;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Animation cho loading */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 1rem;
        }

        .modal-body {
            padding: 1rem;
        }

        .feature-box {
            margin-bottom: 1rem;
        }
    }
</style>
