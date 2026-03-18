<?php

namespace Botble\Onepay\Http\Controllers\Settings;

use Botble\Base\Forms\FormBuilder;
use Botble\Onepay\Forms\Settings\OnepayForm;
use Botble\Onepay\Http\Requests\Settings\OnepayRequest;
use Botble\Setting\Http\Controllers\SettingController;

class OnepayController extends SettingController
{
    public function edit(FormBuilder $formBuilder)
    {
        $this->pageTitle('Page title');

        return $formBuilder->create(OnepayForm::class)->renderForm();
    }

    public function update(OnepayRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
