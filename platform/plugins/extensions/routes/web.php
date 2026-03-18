<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\AdminHelper;

Route::group(['namespace' => 'Botble\Extensions\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'extensions', 'as' => 'extensions.'], function () {
            Route::resource('', 'ExtensionsController')->parameters(['' => 'extensions']);
        });
    });
});
