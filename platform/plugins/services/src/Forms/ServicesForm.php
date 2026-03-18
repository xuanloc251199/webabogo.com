<?php

namespace Botble\Services\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\FormAbstract;
use Botble\Services\Http\Requests\ServicesRequest;
use Botble\Services\Models\Services;

class ServicesForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Services())
            ->setValidatorClass(ServicesRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('price', 'number', [
                'label' => "Giá",
                'required' => true,
                'attr' => [
                    'placeholder' => "Nhập giá",
                    'min' => 0,
                ],
            ])
            ->add('image', MediaImageField::class)
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('image');
    }
}
