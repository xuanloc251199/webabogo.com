<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
// use App\Http\Controllers\Api\V1\NewsController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/orders', [ProfileController::class, 'orders']);
    });

    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);

    Route::get('/{slug}/calendar', [ProductController::class, 'calendar']);
    Route::post('/{slug}/booking-preview', [ProductController::class, 'bookingPreview']);
    Route::get('/{slug}', [ProductController::class, 'show']);

    // Route::get('/news', [NewsController::class, 'index']);
    // Route::get('/news/{slug}', [NewsController::class, 'show']);

    Route::get('/test-mail', function () {
        Mail::raw('Test mail from Abogo', function ($message) {
            $message->to('email_nhan_test@gmail.com')
                ->subject('Test Gmail SMTP');
        });

        return 'OK';
    });
});