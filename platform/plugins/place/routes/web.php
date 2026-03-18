<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\AdminHelper;

Route::group(['namespace' => 'Botble\Place\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'places', 'as' => 'place.'], function () {
            Route::resource('', 'PlaceController')->parameters(['' => 'place']);
        });
    });
});
