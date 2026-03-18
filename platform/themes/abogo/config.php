<?php

use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Theme\Theme;

return [

    /*
    |--------------------------------------------------------------------------
    | Inherit from another theme
    |--------------------------------------------------------------------------
    */

    'inherit' => null, //default

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    | [Notice] these events can be overridden by package config.
    |
    */

    'events' => [

        // Before event inherit from package config and the theme that call before,
        // you can use this event to set meta, breadcrumb template or anything
        // you want inheriting.
        'before' => function ($theme): void {
            // You can remove this line anytime.
        },

        // Listen on event before render a theme,
        // this event should call to assign some assets,
        // breadcrumb template.
        'beforeRenderTheme' => function (Theme $theme): void {
            // Partial composer.
            // $theme->partialComposer('header', function($view) {
            //     $view->with('auth', \Auth::user());
            // });

            // You may use this event to set up your assets.
            $theme->asset()->usePath()->add('bootstrap-style', 'libraries/bootstrap-5.0.2-dist/css/bootstrap.min.css');
            $theme->asset()->add('swiper-style', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
            $theme->asset()->add('select2-style', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
            $theme->asset()->add('daterangepicker-style', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
            $theme->asset()->add('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css');
            $theme->asset()->usePath()->add('lightGallery-style', 'libraries/lightGallery/css/lightgallery.min.css');
            $theme->asset()->usePath()->add('style', 'css/style.css');
            $theme->asset()->container('footer')->usePath()->add('filter', 'css/filter.css');

            $theme->asset()->container('footer')->add('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js');
            $theme->asset()->container('footer')->add('moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js');
            $theme->asset()->container('footer')->add('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11');
            $theme->asset()->container('footer')->add('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js');
            $theme->asset()->container('footer')->add('fontawesome', 'https://kit.fontawesome.com/b726da8c6e.js');
            $theme->asset()->container('footer')->add('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js');
            $theme->asset()->container('footer')->add('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
            $theme->asset()->container('footer')->add('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js');

            $theme->asset()->container('footer')->usePath()->add('lightGallery', 'libraries/lightGallery/js/lightgallery.min.js');
            $theme->asset()->container('footer')->usePath()->add('bootstrap', 'libraries/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js');
            $theme->asset()->container('footer')->usePath()->add('main', 'js/main.js');
            $theme->asset()->container('footer')->usePath()->add('cart', 'js/cart.js');
            $theme->asset()->container('footer')->usePath()->add('popover-module', 'js/popover-module.js');
            $slugProductCategory = \Botble\Slug\Models\Slug::query()->where("reference_type", \Botble\Ecommerce\Models\ProductCategory::class)
                ->pluck("key")->toArray();
            if (in_array(request()->route()->parameter("slug"), $slugProductCategory)) {
                $theme->asset()->container('footer')->usePath()->add('filter_js', 'js/filter.js'); // cho riêng màn có filter
            }
            if (Str::contains(request()->path(), 'checkout/')) {
                $theme->asset()->add('checkout-toastr-css', asset('vendor/core/core/base/libraries/toastr/toastr.min.css'));
//                $theme->asset()->add('checkout-select2-css', asset('vendor/core/core/base/libraries/select2/css/select2.min.css'));
                $theme->asset()->add('checkout-payment-css', asset('vendor/core/plugins/payment/css/payment.css'));

                $theme->asset()->container('footer')->add('checkout', Html::script('vendor/core/plugins/ecommerce/js/checkout.js?v=3.4.0'));
//                $theme->asset()->container('footer')->add('checkout-select2', Html::script('vendor/core/core/base/libraries/select2/js/select2.min.js'));
                if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation()){
                    $theme->asset()->container('footer')->add('checkout-location', Html::script('vendor/core/plugins/location/js/location.js'));
                }

                $theme->asset()->container('footer')->add('checkout-utilities-js',Html::script('vendor/core/plugins/ecommerce/js/utilities.js'));
                $theme->asset()->container('footer')->add('checkout-toastr-js', Html::script('vendor/core/core/base/libraries/toastr/toastr.min.js'));
                $theme->asset()->container('footer')->add('checkout-payment-js', Html::script('vendor/core/plugins/payment/js/payment.js'));
                if (session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count())) {
                    $txt = "";
                    if (session()->has('success_msg')) {
                        $txt = "MainCheckout.showNotice('success', '" . session('success_msg') . "');";
                    }

                    if (session()->has('error_msg')) {
                        $txt = "MainCheckout.showNotice('error', '" . session('error_msg') . "');";
                    }

                    if (isset($errors) && $errors->count()) {
                        $txt = "MainCheckout.showNotice('error', '" . $errors->first() . "');";
                    }

                    $theme->asset()->container('footer')->add(
                        'checkout-inline',
                        <<<HTML

    <script>
        $(document).ready(function () {
            {$txt}
        });
    </script>
    HTML
                    );

                }

            }
            if (function_exists('shortcode')) {
                $theme->composer(['page', 'post'], function (\Botble\Shortcode\View\View $view) {
                    $view->withShortcodes();
                });
            }

        },

        // Listen on event before render a layout,
        // this should call to assign style, script for a layout.
        'beforeRenderLayout' => [
            'default' => function ($theme): void {
                // $theme->asset()->usePath()->add('ipad', 'css/layouts/ipad.css');
            },
        ],
    ],
];
