<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Models\Post;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Facades\Menu;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Support\Arr;

class MenuSeeder extends BaseSeeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Main menu',
                'slug' => 'main-menu',
                'location' => 'main-menu',
                'items' => [
                    [
                        'title' => 'Home',
                        'url' => '/',
                        'icon_font' => 'fa fa-home',
                    ],
                    [
                        'title' => 'Giới thiệu',
                        'reference_id' => 4,
                        'reference_type' => Page::class,
                        'icon_font' => 'fa fa-info-circle',
                    ],
                    [
                        'title' => 'Dịch vụ',
                        'url' => '#',
                        'icon_font' => 'fa fa-cogs',
                    ],
                    [
                        'title' => 'Liên hệ',
                        'reference_id' => 3,
                        'reference_type' => Page::class,
                        'icon_font' => 'fa fa-envelope',
                    ],
                    [
                        'title' => 'Tài khoản',
                        'url' => '#',
                        'icon_font' => 'fa fa-user',
                    ],
                ],
            ],
            [
                'name' => 'Footer widget menu 1',
                'slug' => 'footer-widget-menu-1',
                'items' => [
                    [
                        'title' => 'Giới thiệu',
                        'icon_font' => 'fa-solid fa-compass',
                        'url' => '#',
                    ],
                    [
                        'title' => 'An toàn & Bảo mật',
                        'icon_font' => 'fa-solid fa-shield',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Điều khoản & Điều lệ',
                        'icon_font' => 'fa-solid fa-circle-arrow-right',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Chính sách & Riêng tư',
                        'icon_font' => 'fa-solid fa-circle-arrow-right',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Đạo đức & quy tắc',
                        'icon_font' => 'fa-solid fa-circle-arrow-right',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Tuyển dụng',
                        'icon_font' => 'fa-solid fa-user-plus',
                        'url' => '#',
                    ],
                ],
            ],
            [
                'name' => 'Footer widget menu 2',
                'slug' => 'footer-widget-menu-2',
                'items' => [
                    [
                        'title' => 'Hỏi đáp',
                        'icon_font' => 'fa-solid fa-circle-arrow-right',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Lãnh đạo',
                        'icon_font' => 'fa-solid fa-circle-user',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Nhà đầu tư',
                        'icon_font' => 'fa-solid fa-image-portrait',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Sự kiện',
                        'icon_font' => 'fa-solid fa-location-dot',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Hợp tác với Abogo',
                        'icon_font' => 'fa-solid fa-suitcase',
                        'url' => '#',
                    ],
                ],
            ],
            [
                'name' => 'Footer widget menu 3',
                'slug' => 'footer-widget-menu-3',
                'items' => [
                    [
                        'title' => 'Khách sạn',
                        'icon_font' => 'fa-solid fa-hotel',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Vé máy bay',
                        'icon_font' => 'fa-solid fa-plane',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Resort',
                        'icon_font' => 'fa-solid fa-leaf',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Apartment',
                        'icon_font' => 'fa-solid fa-house',
                        'url' => '#',
                    ],
                    [
                        'title' => 'Spa',
                        'icon_font' => 'fa-solid fa-spa',
                        'url' => '#',
                    ],
                ],
            ],
        ];

        MenuModel::query()->truncate();
        MenuLocation::query()->truncate();
        MenuNode::query()->truncate();

        foreach ($data as $index => $item) {
            $menu = MenuModel::query()->create(Arr::except($item, ['items', 'location']));

            if (isset($item['location'])) {
                $menuLocation = MenuLocation::query()->create([
                    'menu_id' => $menu->id,
                    'location' => $item['location'],
                ]);
                LanguageMeta::saveMetaData($menuLocation);
            }

            foreach ($item['items'] as $menuNode) {
                $this->createMenuNode($index, $menuNode, $menu->id);
            }

            LanguageMeta::saveMetaData($menu);
        }

        Menu::clearCacheMenuItems();
    }

    protected function createMenuNode(int $index, array $menuNode, int $menuId, int $parentId = 0): void
    {
        $menuNode['menu_id'] = $menuId;
        $menuNode['parent_id'] = $parentId;

        if (isset($menuNode['url'])) {
            $menuNode['url'] = str_replace(url(''), '', $menuNode['url']);
        }

        if (Arr::has($menuNode, 'children')) {
            $children = $menuNode['children'];
            $menuNode['has_child'] = true;

            unset($menuNode['children']);
        } else {
            $children = [];
            $menuNode['has_child'] = false;
        }

        $createdNode = MenuNode::query()->create($menuNode);

        if ($children) {
            foreach ($children as $child) {
                $this->createMenuNode($index, $child, $menuId, $createdNode->id);
            }
        }
    }
}
