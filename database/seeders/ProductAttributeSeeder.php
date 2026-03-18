<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductAttributeSet;
use DateTime;

class ProductAttributeSeeder extends BaseSeeder
{
    public function run(): void
    {
        ProductAttributeSet::query()->truncate();
        ProductAttribute::query()->truncate();

        $dateAttrSet = ProductAttributeSet::query()->create([
            'title' => 'Product date',
            'slug' => 'product-date',
            'display_layout' => 'dropdown',
            'is_searchable' => false,
            'is_use_in_product_listing' => false,
        ]);
        $startDate = new DateTime('2025-05-01');
        $endDate = new DateTime('2025-05-31');
        $format = 'Y-m-d';
        while ($startDate <= $endDate) {
            $attr = ProductAttribute::query()->create([
                'attribute_set_id' => $dateAttrSet->getKey(),
                'title' => $startDate->format($format),
                'slug' => $startDate->format($format),
                'is_default' => false,
                'order' => 1,
            ]);

            $startDate->modify('+1 day');
        }

        $ageAttrSet = ProductAttributeSet::query()->create([
            'title' => 'Product age',
            'slug' => 'product-age',
            'display_layout' => 'dropdown',
            'is_searchable' => false,
            'is_use_in_product_listing' => false,
        ]);

        $ageAttributes = [
            [
                'attribute_set_id' => $ageAttrSet->getKey(),
                'title' => 'Người lớn',
                'slug' => 'adult',
                'is_default' => false,
                'order' => 1,
            ],
            [
                'attribute_set_id' => $ageAttrSet->getKey(),
                'title' => 'Trẻ em',
                'slug' => 'child',
                'is_default' => false,
                'order' => 2,
            ],
        ];

        foreach ($ageAttributes as $item) {
            ProductAttribute::query()->create($item);
        }
    }
}
