<?php

namespace Botble\Onepay\Forms\Settings;

use Botble\Onepay\Http\Requests\Settings\OnepayRequest;
use Botble\Setting\Forms\SettingForm;

class OnepayForm extends SettingForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        $this
            ->setSectionTitle('Setting title')
            ->setSectionDescription('Setting description')
            ->setValidatorClass(OnepayRequest::class);
    }
}
