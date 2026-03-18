<?php

namespace Botble\Extensions\Forms\Settings;

use Botble\Extensions\Http\Requests\Settings\ExtensionsRequest;
use Botble\Setting\Forms\SettingForm;

class ExtensionsForm extends SettingForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        $this
            ->setSectionTitle('Setting title')
            ->setSectionDescription('Setting description')
            ->setValidatorClass(ExtensionsRequest::class);
    }
}
