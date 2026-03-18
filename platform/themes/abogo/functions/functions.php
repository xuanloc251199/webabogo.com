<?php

use Botble\Base\Facades\MetaBox;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Menu\Facades\Menu;
use Botble\Theme\Facades\Theme;
use Carbon\CarbonPeriod;

register_page_template([
    'default' => 'Default',
    'homepage' => __('Homepage'),
]);

register_sidebar([
    'id' => 'footer_sidebar',
    'name' => 'Footer sidebar',
    'description' => 'This is footer sidebar for abogo theme',
]);
Menu::addMenuLocation('menu-mobile', 'Menu Mobile');
RvMedia::setUploadPathAndURLToPublic();

add_action(BASE_ACTION_META_BOXES, function ($context, $object) {
    switch (get_class($object)) {
        case Product::class:
            if ($context == 'advanced') {
                MetaBox::addMetaBox(
                    'policies',
                    __('Policies & Rules'),
                    function () {
                        $policy = null;
                        $rule = null;
                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $policy = MetaBox::getMetaData($args[0], 'policy', true);
                            $rule = MetaBox::getMetaData($args[0], 'rule', true);
                        }

                        return Theme::partial('product-policy', compact('policy', 'rule'));
                    },
                    get_class($object),
                    $context
                );
            }
            if ($context == 'side') {
                MetaBox::addMetaBox(
                    'features',
                    __('Features'),
                    function () {
                        $beds = null;
                        $maxAdults = null;
                        $maxChildren = null;
                        $childrenSurplusFee = null;
                        $size = null;
                        $addonFee = null;
                        $serviceFee = null;
                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $beds = MetaBox::getMetaData($args[0], 'beds', true);
                            $maxAdults = MetaBox::getMetaData($args[0], 'max_adults', true);
                            $maxChildren = MetaBox::getMetaData($args[0], 'max_children', true);
                            $childrenSurplusFee = MetaBox::getMetaData($args[0], 'children_surplus_fee', true);
                            $size = MetaBox::getMetaData($args[0], 'size', true);
                            $addonFee = MetaBox::getMetaData($args[0], 'addonFee', true);
                            $serviceFee = MetaBox::getMetaData($args[0], 'serviceFee', true);
                        }

                        return Theme::partial('product-feature', compact('beds', 'maxAdults', 'maxChildren', 'childrenSurplusFee', 'size', 'addonFee', 'serviceFee'));
                    },
                    get_class($object),
                    $context
                );
            }
            break;
    }
}, 75, 2);

add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function ($type, $request, $object) {
    switch (get_class($object)) {
        case Product::class:
            if ($request->has('policy')) {
                MetaBox::saveMetaBoxData($object, 'policy', $request->input('policy'));
            }
            if ($request->has('rule')) {
                MetaBox::saveMetaBoxData($object, 'rule', $request->input('rule'));
            }
            if ($request->has('beds')) {
                MetaBox::saveMetaBoxData($object, 'beds', $request->input('beds'));
            }
            if ($request->has('max_children')) {
                MetaBox::saveMetaBoxData($object, 'max_children', $request->input('max_children'));
            }
            if ($request->has('children_surplus_fee')) {
                MetaBox::saveMetaBoxData($object, 'children_surplus_fee', $request->input('children_surplus_fee'));
            }
            if ($request->has('max_adults')) {
                MetaBox::saveMetaBoxData($object, 'max_adults', $request->input('max_adults'));
            }
            if ($request->has('size')) {
                MetaBox::saveMetaBoxData($object, 'size', $request->input('size'));
            }
            if ($request->has('addonFee')) {
                MetaBox::saveMetaBoxData($object, 'addonFee', $request->input('addonFee'));
            }
            if ($request->has('serviceFee')) {
                MetaBox::saveMetaBoxData($object, 'serviceFee', $request->input('serviceFee'));
            }

            break;
    }
}, 75, 3);

if (!function_exists('format_price_vietnamese')) {
    function format_price_vietnamese($price)
    {
        if ($price >= 1000000) {
            $tr = floor($price / 1000000);
            $tramNghin = floor(($price % 1000000) / 100000);

            return $tramNghin > 0 ? "{$tr}tr{$tramNghin}" : "{$tr}tr";
        }

        return number_format($price);
    }
}

if (!function_exists('get_current_date_price')) {
    function get_current_date_price($product, $date = null, $number_room = 1)
    {
        try {
            $controller = app(\Theme\Abogo\Http\Controllers\AbogoController::class);
            return $controller->getCurrentDatePrice($product, $date, $number_room);
        } catch (\Exception $e) {
            // Fallback to regular price if error occurs
            return $product->sale_price ?: $product->price;
        }
    }
}

//Lấy ra giá của người lớn, trẻ em theo từng ngày với khoảng ngày đã chọn
/*
 * $product_id ID của product đang truy cập
 * $startDate => Carbon
 * $endDate => Carbon
 * $product_type = villa,tour,hotel
 * */

if (!function_exists('getAllPriceOfDateProduct')) {
    function getAllPriceOfDateProduct($product_id, $type): array
    {
        $startDate = Carbon\Carbon::now();
        if ($type == "villa") {
            $product_type = "product-date-villa";
        } elseif ($type == "tour") {
            $product_type = "product-date-tour";
        } else {
            $product_type = "product-date-villa";
        }
        $availableDates = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $product_id)
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.display_layout as attribute_set_display_layout',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.quantity'
            ])
            ->get()
            ->groupBy('variation_id');
        $dates = [];
        foreach ($availableDates as $item) {
            if ($type == "villa") {
                $product_date = $item->where("attribute_set_slug", $product_type)->first();
                if ($product_date->title >= $startDate->format('Y-m-d')) {
                    $dates[$product_date->title] = $product_date->sale_price ?: $product_date->price;
                }

            }

        }
        return $dates;
    }
}


//Lấy ra giá của ngày được chọn ở calendar
if (!function_exists('get_price_chose_calendar_product')) {
    function get_price_chose_calendar_product($product_id, $startDate, $endDate): array
    {
        $availableDates = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $product_id)
            ->whereDate('ec_product_attributes.title', ">=", $startDate)
            ->whereDate('ec_product_attributes.title', "<", $endDate)
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.display_layout as attribute_set_display_layout',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.with_storehouse_management',
                'ec_products.stock_status',
                'ec_products.quantity'
            ])
            ->get();
        $dates = [];
        foreach ($availableDates as $item) {
            if ($item->with_storehouse_management == 1) {
                if ($item->quantity > 0) {
                    $dates[] = $item->sale_price ?: $item->price;
                }
            } else {
                if ($item->stock_status == "in_stock") {
                    $dates[] = $item->sale_price ?: $item->price;
                }
            }
        }
        return $dates;
    }
}
