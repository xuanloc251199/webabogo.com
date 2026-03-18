<?php

use Botble\Ecommerce\Models\ProductVariationItem;
use Illuminate\Support\Collection;

if (!function_exists('get_tour_available_dates')) {
    /**
     * Lấy danh sách ngày có thể đặt tour từ product variations
     * 
     * @param int|string $productId
     * @return Collection
     */
    function get_tour_available_dates(int|string $productId): Collection
    {
        return ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $productId)
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.quantity',
                'ec_products.start_date',
                'ec_products.end_date'
            ])
            ->orderBy('ec_product_attributes.order')
            ->get();
    }
}

if (!function_exists('get_tour_dates_formatted')) {
    /**
     * Lấy danh sách ngày tour đã format
     * 
     * @param int|string $productId
     * @return array
     */
    function get_tour_dates_formatted(int|string $productId): array
    {
        $availableDates = get_tour_available_dates($productId);
        
        $tourDates = [];
        foreach ($availableDates as $attr) {
            $tourDates[] = [
                'date' => $attr->title,
                'attribute_set' => $attr->attribute_set_title,
                'attribute_set_slug' => $attr->attribute_set_slug,
                'variation_id' => $attr->variation_id,
                'price' => $attr->sale_price ?: $attr->price,
                'quantity' => $attr->quantity,
                'start_date' => $attr->start_date,
                'end_date' => $attr->end_date,
                'formatted_price' => format_price($attr->sale_price ?: $attr->price)
            ];
        }
        
        return $tourDates;
    }
}

if (!function_exists('get_tour_dates_by_attribute_set')) {
    /**
     * Lấy ngày tour theo attribute set cụ thể
     * 
     * @param int|string $productId
     * @param string $attributeSetSlug
     * @return array
     */
    function get_tour_dates_by_attribute_set(int|string $productId, string $attributeSetSlug = 'product-date-tour'): array
    {
        $allDates = get_tour_dates_formatted($productId);
        
        return array_filter($allDates, function($date) use ($attributeSetSlug) {
            return $date['attribute_set_slug'] === $attributeSetSlug;
        });
    }
}
