<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Blog\Models\Category;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Faq\Repositories\Interfaces\FaqCategoryInterface;
use Botble\Place\Models\Place;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

app()->booted(function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    Shortcode::register('homepage-section-1', __('Homepage section 1'), __('Homepage section 1'), function (ShortcodeCompiler $shortcode) {
        $firstCategoryIds = collect(explode(',', $shortcode->first_categories))
            ->filter()
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values();

        $secondCategoryIds = collect(explode(',', $shortcode->second_categories))
            ->filter()
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values();
        $firstCategories = ProductCategory::query()
            ->whereIn('id', $firstCategoryIds)
            ->with('slugable')
            ->select('id', 'name', 'image')
            ->get();
        $secondCategories = ProductCategory::query()
            ->whereIn('id', $secondCategoryIds)
            ->with('slugable')
            ->select('id', 'name', 'image')
            ->get();
        return Theme::partial('shortcodes.homepage.homepage-section-1', compact('shortcode', 'firstCategories', 'secondCategories'));
    });
    Shortcode::setAdminConfig('homepage-section-1', function (array $attributes) {
        $categories = ProductCategory::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->pluck('name', 'id');
        return ShortcodeForm::createFromArray($attributes)
            ->add('first_categories',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(__('First category section'))
                    ->choices($categories)
                    ->toArray()
            )
            ->add('second_categories',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(__('Second category section'))
                    ->choices($categories)
                    ->toArray()
            );
    });
    Shortcode::register('homepage-section-2', __('Homepage section 2'), __('Homepage section 2'), function (ShortcodeCompiler $shortcode) {
        $flashSales = FlashSale::query()
            ->notExpired()
            ->wherePublished()
            ->get();
        if (!$flashSales->count()) {
            return null;
        }

        $flashSale = $flashSales->first();

        if (!$flashSale || !$flashSale->products->count()) {
            return null;
        }

        foreach ($flashSales as $item) {
            $item->load([
                'products' => function (BelongsToMany $query) use ($shortcode) {
                    $reviewParams = EcommerceHelper::withReviewsParams();

                    if (EcommerceHelper::isReviewEnabled()) {
                        $query->withAvg($reviewParams['withAvg'][0], $reviewParams['withAvg'][1]);
                    }

                    return $query
                        ->wherePublished()
                        ->limit((int)$shortcode->limit ?: 2)
                        ->withCount($reviewParams['withCount'])
                        ->with(EcommerceHelper::withProductEagerLoadingRelations());
                },
            ]);
        }

        return Theme::partial('shortcodes.homepage.homepage-section-2', compact('shortcode', 'flashSale', 'flashSales'));
    });
    Shortcode::register('homepage-section-3', __('Homepage section 3'), __('Homepage section 1'), function (ShortcodeCompiler $shortcode) {
        $type = $shortcode->type ?? 'featured';

        $products = match ($type) {
            'top_rated' => get_top_rated_products(),
            default => get_featured_products(),
        };

        return Theme::partial('shortcodes.homepage.homepage-section-3', compact('shortcode', 'products'));
    });
    Shortcode::register('homepage-section-4', __('Homepage section 4'), __('Homepage section 1'), function (ShortcodeCompiler $shortcode) {
        $categories = Category::whereHas('posts')
            ->orderBy('order')
            ->get();
        return Theme::partial('shortcodes.homepage.homepage-section-4', compact('shortcode', 'categories'));
    });
    Shortcode::register('homepage-section-5', __('Homepage section 5'), __('Homepage section 1'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.homepage.homepage-section-5', compact('shortcode'));
    });

    Shortcode::setAdminConfig('homepage-section-5', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->add('title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->toArray()
            )
            ->add('desc',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Description'))
                    ->toArray()
            )
            ->add('link',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Link'))
                    ->toArray()
            )
            ->add('image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image'))
                    ->toArray()
            )
            ->add('image_1',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image 1'))
                    ->toArray()
            )
            ->add('title_1',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title 1'))
                    ->toArray()
            )
            ->add('image_2',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image 2'))
                    ->toArray()
            )
            ->add('title_2',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title 2'))
                    ->toArray()
            )
            ->add('image_3',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image 3'))
                    ->toArray()
            )
            ->add('title_3',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title 3'))
                    ->toArray()
            );
    });


    Shortcode::register('homepage-section-6', __('Homepage section 6'), __('Homepage section 1'), function (ShortcodeCompiler $shortcode) {
        $places = Place::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
//            ->whereHas('posts')
                ->orderBy("name")
            ->get();
        return Theme::partial('shortcodes.homepage.homepage-section-6', compact('shortcode', 'places'));
    });

    Shortcode::register('about-item', __('About us page item'), __('About us page item'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.about-us.about-item', compact('shortcode'));
    });

    Shortcode::setAdminConfig('about-item', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->add('icon_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Icon image'))
                    ->toArray()
            )
            ->add('title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->toArray()
            )
            ->add('desc',
                TextField::class,
                TextFieldOption::make()->label(__('Description'))->toArray()
            )
            ->add('link',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/base::forms.link'))
                    ->placeholder('https://')
                    ->toArray()
            )
            ->add('main_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Main image'))
                    ->toArray()
            );
    });

    if (is_plugin_active('faq')) {
        Shortcode::register('faqs', __('Faqs'), __('Faqs'), function (ShortcodeCompiler $shortcode) {
            $params = [
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                ],
                'with' => [
                    'faqs' => function ($query) {
                        $query->wherePublished();
                    },
                ],
                'order_by' => [
                    'faq_categories.order' => 'ASC',
                    'faq_categories.created_at' => 'DESC',
                ],
            ];

            if ($shortcode->category_id) {
                $params['condition']['id'] = $shortcode->category_id;
            }

            $categories = app(FaqCategoryInterface::class)->advancedGet($params);

            return Theme::partial('shortcodes.faq.faqs', compact('shortcode', 'categories'));
        });
    }

    Shortcode::register('contact-profile', "Profile", "Profile", function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.profile', compact('shortcode'));
    });

    Shortcode::register('contact-item-1', __('Ảnh tiêu đề'), __('Ảnh tiêu đề trang contact'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.contact-item-1', compact('shortcode'));
    });

    Shortcode::setAdminConfig('contact-item-1', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->add('image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image'))
                    ->toArray()
            );
    });

    Shortcode::register('contact-item-2', __('Khối thông tin'), __('Khối thông tin trang liên hệ'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.contact-item-2', compact('shortcode'));
    });
    Shortcode::register('contact-item-3', __('Tầm nhìn và xứ mệnh'), __('Khối tầm nhìn và xứ mệnh trang liên hệ'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.contact-item-3', compact('shortcode'));
    });

    Shortcode::register('contact-item-4', __('Quy mô thị trường'), __('Khối quy mô thị trường trang liên hệ'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.contact-item-4', compact('shortcode'));
    });

    Shortcode::register('contact-item-5', __('Đối tác'), __('Khối đối tác trang liên hệ'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.contact-item-5', compact('shortcode'));
    });
    Shortcode::register('contact-item-6', __('Giải thưởng'), __('Khối Giải thưởng trang liên hệ'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.contact.contact-item-6', compact('shortcode'));
    });

    Shortcode::register('product-overview-search', __('product-overview-search'), __('product-overview-search'), function (ShortcodeCompiler $shortcode) {
        $places = Place::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->whereHas('products')
            ->get();
        return Theme::partial('shortcodes.product-overview.search', compact('shortcode', 'places'));
    });
    Shortcode::register('product-overview-category', __('product-overview-category'), __('product-overview-category'), function (ShortcodeCompiler $shortcode) {
        $categories = ProductCategory::query()
            ->has('products')
            ->get();
        return Theme::partial('shortcodes.product-overview.category', compact('shortcode', 'categories'));
    });
    Shortcode::register('product-overview-list-categories-children', __('product-overview-list-categories-children'), __('product-overview-list-categories-children'), function (ShortcodeCompiler $shortcode) {
        $category = ProductCategory::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(["children","children.slugable"])
            ->find($shortcode->category_id);
        if (is_object($category)) {
            $categories = $category->children;

            return Theme::partial('shortcodes.product-overview.list-categories-children', compact('categories'));
        }
        return null;

    });
});
