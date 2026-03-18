<?php

namespace Botble\Onepay\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Forms\PaymentMethodForm;

class OnepayForm extends PaymentMethodForm
{
    public function setup(): void
    {
        parent::setup();
        $this
            ->paymentId(ONEPAY_PAYMENT_METHOD_NAME)
            ->paymentName('OnePay')
            ->paymentDescription(trans('plugins/payment::payment.onepay_description'))
            ->paymentLogo(url('vendor/core/plugins/onepay/images/onepay.svg'))
            ->paymentUrl('https://onepay.com')
            ->paymentInstructions(view('plugins/onepay::instructions')->render())

            ->add(
                'payment_onepay_pay_now_id',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.onepay_pay_now_id'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('pay_now_id', 'onepay'))
                    ->placeholder(trans('plugins/payment::payment.onepay_pay_now_id'))
                    ->toArray()
            )
            ->add(
                'payment_onepay_access_code',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.onepay_access_code'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('access_code', 'onepay'))
                    ->placeholder(trans('plugins/payment::payment.onepay_access_code'))
                    ->attributes(['data-counter' => 400])
                    ->toArray()
            )
            ->add(
                'payment_onepay_hash_code',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.hash_code'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('hash_code', 'onepay'))
                    ->placeholder(trans('plugins/payment::payment.hash_code'))
                    ->toArray()
            );
    }
}
