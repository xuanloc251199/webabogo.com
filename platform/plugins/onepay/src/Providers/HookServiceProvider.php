<?php

namespace Botble\Onepay\Providers;

use Botble\Base\Facades\Html;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Models\Order;
use Botble\Onepay\Class\CreateInvoice;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Onepay\Forms\OnepayForm;
use Botble\Onepay\Services\Gateways\OnepayPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerOnepayMethod'], 1, 2);

        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithOnepay'], 1, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 2);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['ONEPAY'] = ONEPAY_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ONEPAY_PAYMENT_METHOD_NAME) {
                $value = 'Onepay';
            }

            return $value;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ONEPAY_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == ONEPAY_PAYMENT_METHOD_NAME) {
                $data = OnepayPaymentService::class;
            }

            return $data;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
            if ($payment->payment_channel == ONEPAY_PAYMENT_METHOD_NAME) {
                $paymentDetail = (new OnepayPaymentService())->getPaymentDetails($payment->charge_id);

                $data = view('plugins/onepay::detail', ['payment' => $paymentDetail])->render();
            }

            return $data;
        }, 1, 2);

        if (defined('PAYMENT_FILTER_FOOTER_ASSETS')) {
            add_filter(PAYMENT_FILTER_FOOTER_ASSETS, function ($data) {
                if ($this->app->make(OnepayPaymentService::class)->isOnepayApiCharge()) {
                    return $data . view('plugins/onepay::assets')->render();
                }

                return $data;
            }, 1);
        }
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . OnepayForm::create()->renderForm();
    }

    public function registerOnepayMethod(?string $html, array $data): string
    {
        PaymentMethods::method(ONEPAY_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/onepay::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithOnepay(array $data, Request $request): array
    {
        if ($data['type'] !== ONEPAY_PAYMENT_METHOD_NAME) {
            return $data;
        }
//        $createInvoice = new CreateInvoice("TESTONEPAY31", "6BEB2566", "6D0870CDE5F24F34F3915FB0045120D6");
        $createInvoice = new CreateInvoice(get_payment_setting('pay_now_id', 'onepay'), get_payment_setting('access_code', 'onepay'), get_payment_setting('hash_code', 'onepay'));

        // Lưu token vào session để sử dụng trong success callback
        $token = OrderHelper::getOrderSessionToken();
        session(['checkout_token' => $token]);
        $order = Order::find($request->order_id);
        if (!$order){
            return redirect()->back();
        }
        $url = $createInvoice->cInvoice(
            $data["amount"],
            $request->input("address")["phone"],
            route('public.checkout.information', $token),
            route('payments.onepay.success', ['token' => $token]), // Truyền token vào URL success
            $order->code
        );
        $data["checkoutUrl"] = $url;

        return $data;
    }
}
