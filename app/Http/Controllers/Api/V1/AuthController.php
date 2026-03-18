<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RequestRegisterOtpRequest;
use App\Http\Requests\Api\V1\ResendRegisterOtpRequest;
use App\Http\Requests\Api\V1\VerifyRegisterOtpRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Mail\RegisterOtpMail;
use App\Models\CustomerRegisterOtp;
use App\Traits\ApiResponseTrait;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ApiResponseTrait;

    private const OTP_EXPIRE_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;
    private const MAX_RESEND_COUNT = 5;

    public function requestRegisterOtp(RequestRegisterOtpRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();

        $existingCustomer = Customer::query()
            ->where('email', $email)
            ->first();

        if ($existingCustomer) {
            return $this->errorResponse(
                'Email đã tồn tại.',
                ['email' => ['Email đã tồn tại.']],
                422
            );
        }

        $otp = $this->generateOtp();
        $now = now();

        CustomerRegisterOtp::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $request->string('name')->toString(),
                'phone' => $request->input('phone'),
                'password_hash' => Hash::make($request->string('password')->toString()),
                'otp' => $otp,
                'expires_at' => $now->copy()->addMinutes(self::OTP_EXPIRE_MINUTES),
                'last_sent_at' => $now,
            ]
        );

        Mail::to($email)->send(
            new RegisterOtpMail(
                otp: $otp,
                name: $request->string('name')->toString(),
                expireMinutes: self::OTP_EXPIRE_MINUTES,
            )
        );

        return $this->successResponse([
            'email' => $email,
            'expires_in_minutes' => self::OTP_EXPIRE_MINUTES,
        ], 'Mã OTP đã được gửi về email đăng ký.');
    }

    public function resendRegisterOtp(ResendRegisterOtpRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();

        $pending = CustomerRegisterOtp::query()
            ->where('email', $email)
            ->first();

        if (!$pending) {
            return $this->errorResponse(
                'Không tìm thấy yêu cầu đăng ký.',
                ['email' => ['Không tìm thấy yêu cầu đăng ký.']],
                404
            );
        }

        if (
            $pending->last_sent_at &&
            now()->diffInSeconds($pending->last_sent_at) < self::OTP_RESEND_COOLDOWN_SECONDS
        ) {
            return $this->errorResponse(
                'Bạn vừa yêu cầu mã OTP. Vui lòng thử lại sau ít phút.',
                ['otp' => ['Bạn vừa yêu cầu mã OTP. Vui lòng thử lại sau ít phút.']],
                429
            );
        }

        if ($pending->resend_count >= self::MAX_RESEND_COUNT) {
            return $this->errorResponse(
                'Bạn đã vượt quá số lần gửi lại OTP.',
                ['otp' => ['Bạn đã vượt quá số lần gửi lại OTP.']],
                429
            );
        }

        $otp = $this->generateOtp();

        $pending->update([
            'otp' => $otp,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRE_MINUTES),
            'resend_count' => $pending->resend_count + 1,
            'last_sent_at' => now(),
        ]);

        Mail::to($pending->email)->send(
            new RegisterOtpMail(
                otp: $otp,
                name: $pending->name,
                expireMinutes: self::OTP_EXPIRE_MINUTES,
            )
        );

        return $this->successResponse([
            'email' => $pending->email,
            'expires_in_minutes' => self::OTP_EXPIRE_MINUTES,
        ], 'Mã OTP mới đã được gửi lại.');
    }

    public function verifyRegisterOtp(VerifyRegisterOtpRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();
        $otp = $request->string('otp')->toString();

        $pending = CustomerRegisterOtp::query()
            ->where('email', $email)
            ->first();

        if (!$pending) {
            return $this->errorResponse(
                'Không tìm thấy yêu cầu đăng ký.',
                ['email' => ['Không tìm thấy yêu cầu đăng ký.']],
                404
            );
        }

        if ($pending->expires_at->isPast()) {
            return $this->errorResponse(
                'Mã OTP đã hết hạn.',
                ['otp' => ['Mã OTP đã hết hạn.']],
                422
            );
        }

        if ($pending->otp !== $otp) {
            return $this->errorResponse(
                'Mã OTP không chính xác.',
                ['otp' => ['Mã OTP không chính xác.']],
                422
            );
        }

        $existingCustomer = Customer::query()
            ->where('email', $email)
            ->first();

        if ($existingCustomer) {
            $pending->delete();

            return $this->errorResponse(
                'Email đã tồn tại.',
                ['email' => ['Email đã tồn tại.']],
                422
            );
        }

        $customer = DB::transaction(function () use ($pending) {
            $customer = Customer::query()->create([
                'name' => $pending->name,
                'email' => $pending->email,
                'phone' => $pending->phone,
                'password' => $pending->password_hash,
                'status' => CustomerStatusEnum::ACTIVATED,
                'confirmed_at' => Carbon::now(),
            ]);

            $pending->delete();

            return $customer;
        });

        $token = $customer->createToken('mobile-app')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new CustomerResource($customer),
        ], 'Đăng ký tài khoản thành công.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $customer = Customer::query()
            ->where('email', $request->string('email')->toString())
            ->first();

        if (!$customer || !Hash::check($request->string('password')->toString(), $customer->password)) {
            return $this->errorResponse(
                'Thông tin đăng nhập không chính xác.',
                ['email' => ['Thông tin đăng nhập không chính xác.']],
                422
            );
        }

        if (
            (string) (is_object($customer->status) ? $customer->status->getValue() : $customer->status) !== 'activated'
        ) {
            return $this->errorResponse(
                'Tài khoản chưa được kích hoạt.',
                ['account' => ['Tài khoản chưa được kích hoạt.']],
                403
            );
        }

        $token = $customer->createToken('mobile-app')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new CustomerResource($customer),
        ], 'Đăng nhập thành công.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new CustomerResource($request->user()),
            'Lấy thông tin tài khoản thành công.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->successResponse(null, 'Đăng xuất thành công.');
    }

    private function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}