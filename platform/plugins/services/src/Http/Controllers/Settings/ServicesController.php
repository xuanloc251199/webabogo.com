<?php

namespace Botble\Services\Http\Controllers\Settings;

use Botble\Base\Forms\FormBuilder;
use Botble\Services\Forms\Settings\ServicesForm;
use Botble\Services\Http\Requests\Settings\ServicesRequest;
use Botble\Setting\Http\Controllers\SettingController;

class ServicesController extends SettingController
{
    public function edit(FormBuilder $formBuilder)
    {
        $this->pageTitle('Page title');

        return $formBuilder->create(ServicesForm::class)->renderForm();
    }

    public function update(ServicesRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
