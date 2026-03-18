<?php

namespace Botble\Place\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\FormAbstract;
use Botble\Place\Http\Requests\PlaceRequest;
use Botble\Place\Models\Place;

class PlaceForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Place())
            ->setValidatorClass(PlaceRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('image', MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label('Ảnh')
                    ->toArray())
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
