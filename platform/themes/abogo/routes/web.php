<?php

use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;
use Theme\Abogo\Http\Controllers\AbogoController;
use Theme\Abogo\Http\Controllers\TourController;
use Theme\Abogo\Http\Controllers\VillaController;

// Custom routes
// You can delete this route group if you don't need to add your custom routes.
Theme::registerRoutes(function () {

    Route::get('reviews', [AbogoController::class, "getReviews"])
        ->name('public.reviews');
    Route::group(['prefix' => 'ajax', 'as' => 'public.ajax.', 'controller' => VillaController::class], function () {
        Route::get('product-villa-price-calendar', 'getProductPriceByCalendar')
            ->name('product-villa-price-calendar');

        Route::post('add-villa-to-cart', 'addVillaToCart')
            ->name('add-villa-to-cart');
    });

//        Cập nhật lại cartItem của giỏ hàng
    Route::post('reschedule-villa/{product_id}/{rowId?}', [VillaController::class, 'updateCartItem'])
        ->middleware('customer')
        ->name('public.cart.reschedule-villa');
    Route::get('orders-history', [AbogoController::class, 'getOrdersHistory'])
        ->middleware('customer')
        ->name('public.orders.history');


    Route::group(['prefix' => 'ajax', 'as' => 'public.ajax.', 'controller' => TourController::class], function () {

        Route::post('add-tour-to-cart', 'addTourToCart')
            ->name('add-tour-to-cart');
    });

    Route::group(['prefix' => 'ajax', 'as' => 'public.ajax.', 'controller' => AbogoController::class], function () {
        Route::get('filter-category', 'getFilterCategory')
            ->name('filter-category');

        Route::post('add-hotel-to-cart', 'addHotelToCart')
            ->name('add-hotel-to-cart');
        Route::get('suggest-search', 'ajaxSuggestSearch')
            ->name('suggest.search');
        Route::get('product-price-calendar', 'getProductPriceByCalendar')
            ->name('product-price-calendar');
        Route::post('cart/update-service', 'updateCartService')
//            ->middleware('customer')
            ->name("cart.update-service");
        Route::get('product-blogs/{id}', 'getProductBlogs')
            ->name('product-blogs');
        Route::post('filter-rooms-product/{product_id}', 'ajaxGetRoomsProduct')
            ->name('filter-rooms-product');
//        Lấy giá của khoảng ngày
        Route::get('get-price-reschedule/{product_id}', 'getPricereschedule')
            ->name('get.price.reschedule');
    });

//        Cập nhật lại cartItem của giỏ hàng
    Route::post('reschedule/{product_id}/{rowId?}', [AbogoController::class, 'reschedule'])
        ->middleware('customer')
        ->name('public.cart.reschedule');

    Route::group(['prefix' => 'public', 'as' => 'public.theme.', 'controller' => AbogoController::class], function () {
        Route::get('get-product-overview', 'getProductOverview')
            ->name('get-product-overview');
    });
});

Theme::routes();
