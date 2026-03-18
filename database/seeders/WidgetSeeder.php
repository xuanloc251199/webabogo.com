<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Theme\Facades\Theme;
use Botble\Widget\Models\Widget as WidgetModel;

class WidgetSeeder extends BaseSeeder
{
    public function run(): void
    {
        WidgetModel::query()->truncate();

        $data = [
            [
                'widget_id' => 'NewsletterWidget',
                'sidebar_id' => 'top_footer_sidebar',
                'position' => 1,
                'data' => [
                    'id' => 'NewsletterWidget',
                    'name' => 'Sign up to Newsletter',
                    'subtitle' => '...and receive $25 coupon for first shopping.',
                ],
            ],
            [
                'widget_id' => 'SiteInfoWidget',
                'sidebar_id' => 'footer_sidebar',
                'position' => 0,
                'data' => [
                    'id' => 'SiteInfoWidget',
                    'name' => 'Site information',
                ],
            ],
            [
                'widget_id' => 'CustomMenuWidget',
                'sidebar_id' => 'footer_sidebar',
                'position' => 1,
                'data' => [
                    'id' => 'CustomMenuWidget',
                    'name' => 'Categories',
                    'menu_id' => 'product-categories',
                ],
            ],
        ];

        $theme = Theme::getThemeName();

        foreach ($data as $item) {
            $item['theme'] = $theme;
            WidgetModel::query()->create($item);
        }
    }
}
