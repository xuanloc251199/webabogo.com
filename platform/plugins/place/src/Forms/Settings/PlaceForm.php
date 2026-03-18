<?php

namespace Botble\Place\Forms\Settings;

use Botble\Place\Http\Requests\Settings\PlaceRequest;
use Botble\Setting\Forms\SettingForm;

class PlaceForm extends SettingForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        $this
            ->setSectionTitle('Setting title')
            ->setSectionDescription('Setting description')
            ->setValidatorClass(PlaceRequest::class);
    }
}
