<?php

namespace Botble\Extensions\Http\Controllers\Settings;

use Botble\Base\Forms\FormBuilder;
use Botble\Extensions\Forms\Settings\ExtensionsForm;
use Botble\Extensions\Http\Requests\Settings\ExtensionsRequest;
use Botble\Setting\Http\Controllers\SettingController;

class ExtensionsController extends SettingController
{
    public function edit(FormBuilder $formBuilder)
    {
        $this->pageTitle('Page title');

        return $formBuilder->create(ExtensionsForm::class)->renderForm();
    }

    public function update(ExtensionsRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
