<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Arr;

class ProductCategorySeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('product-categories');

        $categories = [
            [
                'name' => 'Villa',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Resort',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Khách sạn',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Apartment',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Spa',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Du thuyền',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Tour',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Combo',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Checkin',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Checkout',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Dịch vụ',
                'image' => 'product-categories/5.jpg',
            ], [
                'name' => 'Thuê xe',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Quà tặng',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Review',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Vé',
                'image' => 'product-categories/5.jpg',
            ],
            [
                'name' => 'Golf',
                'image' => 'product-categories/5.jpg',
            ],
        ];

        ProductCategory::query()->truncate();

        foreach ($categories as $index => $item) {
            $this->createCategoryItem($index, $item);
        }
    }

    protected function createCategoryItem(int $index, array $category, int $parentId = 0): void
    {
        $category['parent_id'] = $parentId;
        $category['order'] = $index;

        if (Arr::has($category, 'children')) {
            $children = $category['children'];
            unset($category['children']);
        } else {
            $children = [];
        }

        $createdCategory = ProductCategory::query()->create($category);

        SlugHelper::createSlug($createdCategory);

        if ($children) {
            foreach ($children as $childIndex => $child) {
                $this->createCategoryItem($childIndex, $child, $createdCategory->id);
            }
        }
    }
}
