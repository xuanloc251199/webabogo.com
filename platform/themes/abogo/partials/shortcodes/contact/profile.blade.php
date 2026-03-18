<div class="account-page container">
    <!-- Avatar -->
    <div class="account-avatar text-center">
        @if(theme_option('profile_avatar'))
            {{ RvMedia::image(theme_option('profile_avatar'), theme_option('profile_name', 'Abogo'), attributes: ['class' => 'bb-customer-profile-avatar-img avatar-img']) }}
        @else
            <img src="{{ Theme::asset()->url('images/common/avatar.jpg') }}" alt="{{ theme_option('profile_name', 'Abogo') }}" class="bb-customer-profile-avatar-img avatar-img">
        @endif
        <h3 class="mt-3">{{ theme_option('profile_name', 'Abogo') }}</h3>
    </div>

    <!-- Khối tính năng -->
    <div class="account-features">
        <div class="row">
            <!-- Hàng 1 -->
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('contact_phone'))
                        <a href="tel:{{ theme_option('contact_phone') }}" class="text-white text-decoration-none">
                            <i class="fas fa-phone"></i>
                            <span>Gọi</span>
                        </a>
                    @else
                        <i class="fas fa-phone"></i>
                        <span>Gọi</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    <i class="fas fa-comments"></i>
                    <span>Nhắn tin</span>
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('contact_email'))
                        <a href="mailto:{{ theme_option('contact_email') }}" class="text-white text-decoration-none">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </a>
                    @else
                        <i class="fas fa-envelope"></i>
                        <span>Email</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('website_url'))
                        <a href="{{ theme_option('website_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fas fa-globe"></i>
                            <span>Website</span>
                        </a>
                    @else
                        <i class="fas fa-globe"></i>
                        <span>Website</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('facebook_url'))
                        <a href="{{ theme_option('facebook_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                    @else
                        <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('messenger_url'))
                        <a href="{{ theme_option('messenger_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-facebook-messenger"></i>
                            <span>Messenger</span>
                        </a>
                    @else
                        <i class="fab fa-facebook-messenger"></i>
                        <span>Messenger</span>
                    @endif
                </div>
            </div>

            <!-- Hàng 2 -->
            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('instagram_url'))
                        <a href="{{ theme_option('instagram_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                    @else
                        <i class="fab fa-instagram"></i>
                        <span>Instagram</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('wechat_url'))
                        <a href="{{ theme_option('wechat_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-weixin"></i>
                            <span>We Chat</span>
                        </a>
                    @else
                        <i class="fab fa-weixin"></i>
                        <span>We Chat</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('line_url'))
                        <a href="{{ theme_option('line_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-line"></i>
                            <span>Line</span>
                        </a>
                    @else
                        <i class="fab fa-line"></i>
                        <span>Line</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('whatsapp_number'))
                        <a href="https://wa.me/{{ theme_option('whatsapp_number') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-whatsapp"></i>
                            <span>What App</span>
                        </a>
                    @else
                        <i class="fab fa-whatsapp"></i>
                        <span>What App</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('tiktok_url'))
                        <a href="{{ theme_option('tiktok_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fab fa-tiktok"></i>
                            <span>Tiktok</span>
                        </a>
                    @else
                        <i class="fab fa-tiktok"></i>
                        <span>Tiktok</span>
                    @endif
                </div>
            </div>

            <div class="col-md-2 col-3 mb-4">
                <div class="feature-box rounded-20">
                    @if(theme_option('kakaotalk_url'))
                        <a href="{{ theme_option('kakaotalk_url') }}" target="_blank" class="text-white text-decoration-none">
                            <i class="fas fa-comment-dots"></i>
                            <span>Kakaotalk</span>
                        </a>
                    @else
                        <i class="fas fa-comment-dots"></i>
                        <span>Kakaotalk</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling cho profile page */
    .account-page {
        padding: 2rem 0;
    }

    .account-avatar {
        margin-bottom: 3rem;
    }

    .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .account-avatar h3 {
        color: #333;
        font-weight: 600;
        margin-top: 1rem;
    }

    /* Feature boxes styling */
    .feature-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 1.5rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .feature-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .feature-box i {
        font-size: 1.8rem;
        color: white;
        margin-bottom: 0.5rem;
    }

    .feature-box span {
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .feature-box a {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
    }

    .feature-box a i {
        margin-bottom: 0.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .account-page {
            padding: 1rem 0;
        }

        .avatar-img {
            width: 100px;
            height: 100px;
        }

        .feature-box {
            padding: 1rem 0.5rem;
            min-height: 80px;
        }

        .feature-box i {
            font-size: 1.5rem;
        }

        .feature-box span {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .feature-box {
            margin-bottom: 0.5rem;
        }

        .feature-box span {
            font-size: 0.75rem;
        }
    }
</style>
