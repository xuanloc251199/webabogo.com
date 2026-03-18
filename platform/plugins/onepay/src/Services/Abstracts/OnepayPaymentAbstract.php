<?php

namespace Botble\Onepay\Services\Abstracts;

use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Botble\Onepay\Supports\OnepayHelper;
use Exception;
use Onepay\Charge;
use Onepay\Exception\ApiConnectionException;
use Onepay\Exception\ApiErrorException;
use Onepay\Exception\AuthenticationException;
use Onepay\Exception\CardException;
use Onepay\Exception\InvalidRequestException;
use Onepay\Exception\RateLimitException;
use Onepay\Refund;
use Onepay\Onepay;

abstract class OnepayPaymentAbstract
{
    use PaymentErrorTrait;

    protected ?string $token = null;

    protected float $amount;

    protected string $currency;

    protected string $chargeId;

    protected bool $supportRefundOnline = true;

    public function getSupportRefundOnline(): bool
    {
        return $this->supportRefundOnline;
    }

    public function execute(array $data): ?string
    {
        $this->token = request()->input('onepayToken');

        $chargeId = null;

        try {
            $chargeId = $this->makePayment($data);
        } catch (CardException $exception) {
            $this->setErrorMessageAndLogging($exception, 1); // Since it's a decline, \Onepay\Error\Card will be caught
        } catch (RateLimitException $exception) {
            $this->setErrorMessageAndLogging($exception, 2); // Too many requests made to the API too quickly
        } catch (InvalidRequestException $exception) {
            $this->setErrorMessageAndLogging($exception, 3); // Invalid parameters were supplied to Onepay's API
        } catch (AuthenticationException $exception) {
            $this->setErrorMessageAndLogging($exception, 4); // Authentication with Onepay's API failed (maybe you changed API keys recently)
        } catch (ApiConnectionException $exception) {
            $this->setErrorMessageAndLogging($exception, 5); // Network communication with Onepay failed
        } catch (ApiErrorException $exception) {
            $this->setErrorMessageAndLogging($exception, 6); // Display a very generic error to the user, and maybe send yourself an email
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 7); // Something else happened, completely unrelated to Onepay
        }

        return $chargeId;
    }

    abstract public function makePayment(array $data): ?string;

    abstract public function afterMakePayment(string $chargeId, array $data);

    public function getPaymentDetails(string $chargeId): ?Charge
    {
        if (! $this->setClient()) {
            return null;
        }

        try {
            return Charge::retrieve($chargeId);
        } catch (Exception) {
            return null;
        }
    }

    public function setClient(): bool
    {
        $secret = get_payment_setting('secret', 'onepay');
        $clientId = get_payment_setting('client_id', 'onepay');

        if (! $secret || ! $clientId) {
            return false;
        }

        Onepay::setApiKey($secret);
        Onepay::setClientId($clientId);

        return true;
    }

    public function setCurrency($currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function refundOrder(string $paymentId, float|string $totalAmount, array $options = []): array
    {
        if (! $this->setClient()) {
            return [
                'error' => true,
                'message' => trans('plugins/payment::payment.invalid_settings', ['name' => 'Onepay']),
            ];
        }

        $multiplier = OnepayHelper::getOnepayCurrencyMultiplier($this->currency);

        if ($multiplier > 1) {
            $totalAmount = (int) (round((float) $totalAmount, 2) * $multiplier);
        }

        try {
            $response = Refund::create([
                'charge' => $paymentId,
                'amount' => $totalAmount,
                'metadata' => $options,
            ]);

            if ($response->status == 'succeeded') {
                return [
                    'error' => false,
                    'message' => $response->status,
                    'data' => (array) $response,
                ];
            }

            return [
                'error' => true,
                'message' => trans('plugins/payment::payment.status_is_not_completed'),
            ];
        } catch (Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
