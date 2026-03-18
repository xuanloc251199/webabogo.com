<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $name,
        public int $expireMinutes = 10,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Mã xác nhận đăng ký tài khoản Abogo')
            ->html("
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #111;'>
                    <h2 style='margin-bottom: 12px;'>Xác nhận đăng ký tài khoản Abogo</h2>
                    <p>Xin chào <strong>{$this->name}</strong>,</p>
                    <p>Mã OTP để hoàn tất đăng ký tài khoản của bạn là:</p>
                    <div style='font-size: 32px; font-weight: bold; letter-spacing: 8px; margin: 20px 0; color: #000;'>
                        {$this->otp}
                    </div>
                    <p>Mã có hiệu lực trong <strong>{$this->expireMinutes} phút</strong>.</p>
                    <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email.</p>
                    <p>Abogo</p>
                </div>
            ");
    }
}