<?php

use Botble\Onepay\Http\Controllers\OnepayController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment/onepay')
    ->name('payments.onepay.')
    ->group(function () {
        Route::post('webhook', [OnepayController::class, 'webhook'])->name('webhook');

        Route::middleware(['web', 'core'])->group(function () {
            Route::get('ipn', [OnepayController::class, 'success'])->name('success');
            Route::get('error', [OnepayController::class, 'error'])->name('error');
        });
    });

