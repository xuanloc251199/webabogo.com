<?php

namespace Botble\Blog\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\IsFeaturedFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TagFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TreeCategoryField;
use Botble\Base\Forms\FormAbstract;
use Botble\Blog\Http\Requests\PostRequest;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Place\Models\Place;

class PostForm extends FormAbstract
{
    public function setup(): void
    {

        $selectedProductCategories = [];
        $productCategories = ProductCategory::query()->pluck('name', 'id')->all();
        if ($this->getModel()) {
            $selectedProductCategories = $this->getModel()->product_categories()->pluck('id')->all();

        }
        $selectedPlaces = [];
        $places = Place::query()->pluck('name', 'id')->all();
        if ($this->getModel()) {
            $selectedPlaces = $this->getModel()->places()->pluck('place_id')->all();

        }
        $this
            ->model(Post::class)
            ->setValidatorClass(PostRequest::class)
            ->hasTabs()
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add(
                'is_featured',
                OnOffField::class,
                IsFeaturedFieldOption::make()
                    ->toArray()
            )
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes()->toArray())
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())

            ->when(get_post_formats(true), function (PostForm $form, array $postFormats) {
                if (count($postFormats) > 1) {
                    $choices = [];

                    foreach ($postFormats as $postFormat) {
                        $choices[$postFormat[0]] = $postFormat[1];
                    }

                    $form
                        ->add(
                            'format_type',
                            RadioField::class,
                            RadioFieldOption::make()
                                ->label(trans('plugins/blog::posts.form.format_type'))
                                ->choices($choices)
                                ->toArray()
                        );
                }
            })

            ->add(
                'product_categories[]',
                MultiCheckListField::class, [
                    'label' => trans('Danh mục sản phẩm'),
                    'choices' => $productCategories,
                    'value' => old('services', $selectedProductCategories),
                ]
            )
            ->add(
                'places[]',
                MultiCheckListField::class, [
                    'label' => trans('Chọn Địa điểm'),
                    'choices' => $places,
                    'value' => old('services', $selectedPlaces),
                ]
            )
            ->add(
                'categories[]',
                TreeCategoryField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/blog::posts.form.categories'))
                    ->choices(get_categories_with_children())
                    ->when($this->getModel()->getKey(), function (SelectFieldOption $fieldOption) {
                        return $fieldOption->selected($this->getModel()->categories()->pluck('category_id')->all());
                    }, function (SelectFieldOption $fieldOption) {
                        return $fieldOption
                            ->selected(Category::query()
                                ->where('is_default', 1)
                                ->pluck('id')
                                ->all());
                    })
                    ->toArray()
            )
            ->add('image', MediaImageField::class)
            ->add(
                'tag',
                TagField::class,
                TagFieldOption::make()
                    ->label(trans('plugins/blog::posts.form.tags'))
                    ->when($this->getModel()->getKey(), function (TagFieldOption $fieldOption) {
                        return $fieldOption
                            ->selected(
                                $this
                                    ->getModel()
                                    ->tags()
                                    ->select('name')
                                    ->get()
                                    ->map(fn(Tag $item) => $item->name)
                                    ->implode(',')
                            );
                    })
                    ->placeholder(trans('plugins/blog::base.write_some_tags'))
                    ->ajaxUrl(route('tags.all'))
                    ->toArray()
            )
            ->setBreakFieldPoint('status');
    }
}
