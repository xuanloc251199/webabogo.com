<?php

namespace Botble\Place\Http\Controllers\Settings;

use Botble\Base\Forms\FormBuilder;
use Botble\Place\Forms\Settings\PlaceForm;
use Botble\Place\Http\Requests\Settings\PlaceRequest;
use Botble\Setting\Http\Controllers\SettingController;

class PlaceController extends SettingController
{
    public function edit(FormBuilder $formBuilder)
    {
        $this->pageTitle('Page title');

        return $formBuilder->create(PlaceForm::class)->renderForm();
    }

    public function update(PlaceRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
