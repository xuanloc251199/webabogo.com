@if (setting('payment_onepay_status') == 1)
    <x-plugins-payment::payment-method
        :name="ONEPAY_PAYMENT_METHOD_NAME"
        paymentName="Onepay"
        :supportedCurrencies="(new Botble\Onepay\Services\Gateways\OnepayPaymentService)->supportedCurrencyCodes()"
    >
{{--        @if (get_payment_setting('payment_type', ONEPAY_PAYMENT_METHOD_NAME, 'onepay_api_charge') == 'onepay_api_charge')--}}
{{--            <div class="card-checkout" style="max-width: 350px">--}}
{{--                <div class="form-group mt-3 mb-3">--}}
{{--                    <div class="onepay-card-wrapper"></div>--}}
{{--                </div>--}}

{{--                <div @class(['form-group mb-3', 'has-error' => $errors->has('number') || $errors->has('expiry')])>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-sm-8">--}}
{{--                            <input--}}
{{--                                class="form-control"--}}
{{--                                id="onepay-number"--}}
{{--                                data-onepay="number"--}}
{{--                                type="text"--}}
{{--                                placeholder="{{ trans('plugins/payment::payment.card_number') }}"--}}
{{--                                autocomplete="off"--}}
{{--                            >--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-4">--}}
{{--                            <input--}}
{{--                                class="form-control"--}}
{{--                                id="onepay-exp"--}}
{{--                                data-onepay="exp"--}}
{{--                                type="text"--}}
{{--                                placeholder="{{ trans('plugins/payment::payment.mm_yy') }}"--}}
{{--                                autocomplete="off"--}}
{{--                            >--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div @class(['form-group mb-3', 'has-error' => $errors->has('name') || $errors->has('cvc')])>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-sm-8">--}}
{{--                            <input--}}
{{--                                class="form-control"--}}
{{--                                id="onepay-name"--}}
{{--                                data-onepay="name"--}}
{{--                                type="text"--}}
{{--                                placeholder="{{ trans('plugins/payment::payment.full_name') }}"--}}
{{--                                autocomplete="off"--}}
{{--                            >--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-4">--}}
{{--                            <input--}}
{{--                                class="form-control"--}}
{{--                                id="onepay-cvc"--}}
{{--                                data-onepay="cvc"--}}
{{--                                type="text"--}}
{{--                                placeholder="{{ trans('plugins/payment::payment.cvc') }}"--}}
{{--                                autocomplete="off"--}}
{{--                            >--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div id="payment-onepay-key" data-value="{{ get_payment_setting('client_id', ONEPAY_PAYMENT_METHOD_NAME) }}"></div>--}}
{{--        @endif--}}
    </x-plugins-payment::payment-method>
@endif
