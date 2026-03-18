<?php

namespace Botble\Services\Forms\Settings;

use Botble\Services\Http\Requests\Settings\ServicesRequest;
use Botble\Setting\Forms\SettingForm;

class ServicesForm extends SettingForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        $this
            ->setSectionTitle('Setting title')
            ->setSectionDescription('Setting description')
            ->setValidatorClass(ServicesRequest::class);
    }
}
