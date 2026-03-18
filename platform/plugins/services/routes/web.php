<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\AdminHelper;

Route::group(['namespace' => 'Botble\Services\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
            Route::resource('', 'ServicesController')->parameters(['' => 'services']);
        });
    });
});
