<?php

namespace Database\Seeders;

use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\Page\Models\Page;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;

class PageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $faker = fake();

        $pages = [
            [
                'name' => 'Homepage',
                'content' =>
                    Html::tag(
                        'div',
                        '[homepage-section-1][/homepage-section-1]'
                    ) .
                    Html::tag(
                        'div',
                        '[homepage-section-2][/homepage-section-2]'
                    ) .
                    Html::tag(
                        'div',
                        '[homepage-section-3][/homepage-section-3]'
                    ) .
                    Html::tag(
                        'div',
                        '[homepage-section-4][/homepage-section-4]'
                    ) .
                    Html::tag(
                        'div',
                        '[homepage-section-5][/homepage-section-5]'
                    ) .
                    Html::tag(
                        'div',
                        '[homepage-section-6][/homepage-section-6]'
                    )
                ,
                'template' => 'homepage',
            ],
            [
                'name' => 'Blog',
                'content' => Html::tag('p', '---'),
            ],
            [
                'name' => 'Contact',
                'content' => Html::tag('div',
                    '[custom-html]&lt;div class="contact-section container position-relative"&gt;[/custom-html]' .
                    '[custom-html]&lt;ul class="nav nav-tabs mb-4" id="myTab" role="tablist" &gt;[/custom-html]' .
                    '[custom-html]&lt;li class="nav-item" role="presentation" &gt;[/custom-html]' .
                    '[custom-html]&lt;button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                        type="button" role="tab" aria-controls="home" aria-selected="true" &gt;[/custom-html]' . 'Profile Abogo' .
                    '[custom-html]&lt;/button&gt;[/custom-html]'.
                    '[custom-html]&lt;/li&gt;[/custom-html]'.
                    '[custom-html]&lt;li class="nav-item" role="presentation" &gt;[/custom-html]' .
                    '[custom-html]&lt;button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                        type="button" role="tab" aria-controls="profile" aria-selected="false" &gt;[/custom-html]' . 'Liên hệ Online' .
                    '[custom-html]&lt;/button&gt;[/custom-html]'.
                    '[custom-html]&lt;/li&gt;[/custom-html]'.
                    '[custom-html]&lt;/ul&gt;[/custom-html]'.

                    '[custom-html]&lt;div class="tab-content" id="myTabContent"&gt;[/custom-html]' .
                    '[custom-html]&lt;div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab"&gt;[/custom-html]' .
                    '[custom-html]&lt;div class="row d-flex align-items-stretch"&gt;[/custom-html]' .

                    '[contact-item-1][/contact-item-1]' .
                    '[contact-item-2][/contact-item-2]' .
                    '[contact-item-3][/contact-item-3]' .
                    '[contact-item-4][/contact-item-4]' .
                    '[contact-item-5][/contact-item-5]' .
                    '[contact-item-6][/contact-item-6]' .

                    '[custom-html]&lt;/div&gt;[/custom-html]'.
                    '[custom-html]&lt;/div&gt;[/custom-html]'.

                    '[custom-html]&lt;div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab"&gt;[/custom-html]' .
                    '[custom-html]&lt;/div&gt;[/custom-html]'.


                    '[custom-html]&lt;/div&gt;[/custom-html]'.

                    '[custom-html]&lt;/div&gt;[/custom-html]'

                    )
                ,
            ],
            [
                'name' => 'About us',
                'content' => Html::tag(
                    'div',
                    '[custom-html]&lt;div class="about-us container"&gt;[/custom-html]' .
                    '[custom-html]&lt;div class="row"&gt;[/custom-html]' .
                    '[about-item title="Giới Thiệu" desc="Abogo không bán sản phẩm bình thường mà là kinh doanh kiến tạo lòng trắc ẩn. Để thấy được triết lý kinh doanh của Abogo luôn hướng đến trải nghiệm của khách hàng."][/about-item]' .
                    '[about-item title="Sứ Mệnh Và Tầm Nhìn" desc="Abogo không bán sản phẩm bình thường mà là kinh doanh kiến tạo lòng trắc ẩn. Để thấy được triết lý kinh doanh của Abogo luôn hướng đến trải nghiệm của khách hàng."][/about-item]' .
                    '[about-item title="Đối Tác" desc="Abogo không bán sản phẩm bình thường mà là kinh doanh kiến tạo lòng trắc ẩn. Để thấy được triết lý kinh doanh của Abogo luôn hướng đến trải nghiệm của khách hàng."][/about-item]' .
                    '[about-item title="Tuyển Dụng" desc="Abogo không bán sản phẩm bình thường mà là kinh doanh kiến tạo lòng trắc ẩn. Để thấy được triết lý kinh doanh của Abogo luôn hướng đến trải nghiệm của khách hàng."][/about-item]' .
                    '[custom-html]&lt;/div&gt;[/custom-html]' .
                    '[custom-html]&lt;/div&gt;[/custom-html]'
                    ,
                ),
            ],
            [
                'name' => 'FAQ',
                'content' => Html::tag('div', '[faqs][/faqs]'),
            ],
        ];

        Page::query()->truncate();

        foreach ($pages as $item) {
            $item['user_id'] = 1;

            if (!isset($item['template'])) {
                $item['template'] = 'default';
            }

            $page = Page::query()->create(
                Arr::except(
                    $item,
                    ['header_style', 'expanding_product_categories_on_the_homepage']
                )
            );

            if (isset($item['expanding_product_categories_on_the_homepage'])) {
                MetaBox::saveMetaBoxData(
                    $page,
                    'expanding_product_categories_on_the_homepage',
                    $item['expanding_product_categories_on_the_homepage']
                );
            }

            SlugHelper::createSlug($page);
        }
    }
}
