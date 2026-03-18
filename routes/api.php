<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::prefix('v1')->group(function () {
    Route::get('/ping', function () {
        return response()->json([
            'success' => true,
            'message' => 'Abogo API is working',
        ]);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register/request-otp', [AuthController::class, 'requestRegisterOtp']);
        Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp']);
        Route::post('/register/resend-otp', [AuthController::class, 'resendRegisterOtp']);

        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    Route::get('/test-mail', function () {
        Mail::raw('Test mail from Abogo', function ($message) {
            $message->to('email_nhan_test@gmail.com')
                ->subject('Test Gmail SMTP');
        });

        return 'OK';
    });
});
