<?php

namespace Database\Seeders;

use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\Page\Models\Page;
use Botble\Place\Models\Place;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Arr;

class PlaceSeeder extends BaseSeeder
{
    public function run(): void
    {
        $faker = fake();

        $places = [
            [
                'name' => 'Sapa',
                'description' => $faker->paragraph(3),
            ],
            [
                'name' => 'HCM',
                'description' => $faker->paragraph(3),
            ],
            [
                'name' => 'Gia Lai',
                'description' => $faker->paragraph(3),
            ],
            [
                'name' => 'Phú Quốc',
                'description' => $faker->paragraph(3),
            ],
            [
                'name' => 'Đà Lạt',
                'description' => $faker->paragraph(3),
            ], [
                'name' => 'Đà Nẵng',
                'description' => $faker->paragraph(3),
            ], [
                'name' => 'Hội An',
                'description' => $faker->paragraph(3),
            ], [
                'name' => 'Hà Nội',
                'description' => $faker->paragraph(3),
            ], [
                'name' => 'Hạ Long',
                'description' => $faker->paragraph(3),
            ], [
                'name' => 'Vũng Tàu',
                'description' => $faker->paragraph(3),
            ], [
                'name' => 'Nha Trang',
                'description' => $faker->paragraph(3),
            ],
        ];

        Place::query()->truncate();

        foreach ($places as $item) {
            $place = Place::query()->create($item);

            SlugHelper::createSlug($place);
        }
    }
}
