<?php

use Botble\Base\Facades\Html;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Widgets\ValueObjects\CoreSimpleMenuItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FooterMenuWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Footer Menu'),
            'description' => __('Widget description'),
            'items' => [],
        ]);
    }

    public function adminConfig(): array
    {
        $fields = [
            [
                'type' => 'text',
                'label' => trans('packages/widget::widget.widget_menu_label'),
                'required' => true,
                'attributes' => [
                    'name' => 'label',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => trans('packages/widget::widget.widget_menu_url'),
                'required' => true,
                'attributes' => [
                    'name' => 'url',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => trans('Icon'),
                'required' => false,
                'attributes' => [
                    'name' => 'icon',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'fas fa-building',
                    ],
                ],
                'helper' => __(
                    'Search for icon :link!',
                    [
                        'link' => Html::link('https://fontawesome.com/search?ic=free', 'Here'),
                    ]
                ),
            ],
            [
                'type' => 'text',
                'label' => trans('packages/widget::widget.widget_menu_attributes'),
                'attributes' => [
                    'name' => 'attributes',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'rel="nofollow" aria-label="Home"',
                    ],
                ],
            ],
            [
                'type' => 'onOff',
                'label' => trans('packages/widget::widget.widget_menu_is_open_new_tab'),
                'attributes' => [
                    'name' => 'is_open_new_tab',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
        ];

        return apply_filters('widget_menu_admin_config', [
            'fields' => $fields,
        ], $this);
    }


    public function data(): array|Collection
    {
        $items = $this->getConfig('items', []);

        if ($items === '[]') {
            $this->data['items'] = collect();

            return $this->data;
        }

        return array_merge($this->data, [
            'items' => collect($items)
                ->reject(function ($item) {
                    return !Arr::has($item, '0.key');
                })
                ->map(function ($item) {
                    return new CoreSimpleMenuItem($item);
                }),
        ]);
    }
}
