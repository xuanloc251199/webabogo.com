<?php

namespace Botble\Extensions\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\FormAbstract;
use Botble\Extensions\Http\Requests\ExtensionsRequest;
use Botble\Extensions\Models\Extensions;

class ExtensionsForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Extensions())
            ->setValidatorClass(ExtensionsRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('image', MediaImageField::class)
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
