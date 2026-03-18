<?php

namespace Botble\Onepay\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Onepay\Http\Requests\OnepayRequest;
use Botble\Onepay\Models\Onepay;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Onepay\Tables\OnepayTable;
use Botble\Onepay\Forms\OnepayForm;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Customer;
use Botble\Onepay\Http\Requests\OnepayPaymentCallbackRequest;
use Botble\Onepay\Services\Gateways\OnepayPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OnepayController extends BaseController
{

    public function webhook(Request $request)
    {
        // OnePay webhook implementation
        // Có thể implement sau nếu OnePay hỗ trợ webhook
        return response()->noContent();
    }

    public function success(
        Request $request,
        BaseHttpResponse $response
    ) {
        try {
            
            // Lấy các tham số từ OnePay callback
            $vpc_TxnResponseCode = $request->input('vpc_TxnResponseCode');
            $vpc_TransactionNo = $request->input('vpc_TransactionNo');
            $vpc_Amount = $request->input('vpc_Amount');
            $vpc_OrderInfo = $request->input('vpc_OrderInfo');
            $vpc_MerchTxnRef = $request->input('vpc_MerchTxnRef');
            if (!$this->checkOnePaySecureHash($request)) {
                
                $request->session()->flash('error_msg', true);
                return $response
                    ->setNextUrl(route('public.cart'))
                    ->setMessage(__('Invalid payment signature!'));
            }
            // Lấy token từ route parameter hoặc session
            // $token = $token ?: session('checkout_token');
            // if (!$token) {
            //     return $response
            //         ->setError()
            //         ->setNextUrl(route("public.cart"))
            //         ->setMessage(__('Invalid checkout session!'));
            // }

            // Tìm order theo token
            $order = Order::query()
                ->where('code', $vpc_OrderInfo)
                ->first();
            if (!$order) {
                return $response
                    ->setError()
                    ->setNextUrl(route("public.cart"))
                    ->setMessage(__('Order not found!'));
            }
            $chargeId = $vpc_TransactionNo ?: 'ONEPAY_' . time();

            // Kiểm tra mã phản hồi từ OnePay và cập nhật trạng thái đơn hàng
            if (
                    $vpc_TxnResponseCode === '0'
                    && (int)$vpc_Amount === (int)($order->amount * 100)
                    && $vpc_MerchTxnRef === $order->code
                ){
                
                // Thanh toán thành công
                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $order->amount,
                    'currency' => 'VND',
                    'charge_id' => $chargeId,
                    'order_id' => [$order->id],
                    'customer_id' => $order->user_id,
                    'customer_type' => Customer::class,
                    'payment_channel' => ONEPAY_PAYMENT_METHOD_NAME,
                    'status' => PaymentStatusEnum::COMPLETED,
                ]);
                $request->session()->flash('success_msg', true);

                return $response
//                    ->setNextUrl(PaymentHelper::getRedirectURL() . '?charge_id=' . $chargeId)
                    ->setNextUrl(route("public.cart"))
                    ->setMessage(__('Đơn đặt của bạn đã được gửi đến Abogo. Vui lòng đợi nhân viên liên hệ lại với bạn!'));
            } else {
                // Thanh toán thất bại - tạo payment record với status FAILED
                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $order->amount,
                    'currency' => 'VND',
                    'charge_id' => $chargeId,
                    'order_id' => [$order->id],
                    'customer_id' => $order->user_id,
                    'customer_type' => Customer::class,
                    'payment_channel' => ONEPAY_PAYMENT_METHOD_NAME,
                    'status' => PaymentStatusEnum::FAILED,
                ]);
                $request->session()->flash('error_msg', true);
                return $response
                    ->setNextUrl(route("public.cart"))
                    ->setMessage(__('Thanh toán thất bại! Mã lỗi: ' . $vpc_TxnResponseCode));
            }
            
        } catch (\Exception $exception) {
            BaseHelper::logError($exception);
            return $response
                ->setError()
                ->setNextUrl(route("public.cart"))
                ->withInput()
                ->setMessage($exception->getMessage() ?: __('Payment failed!'));
        }
    }
    private function checkOnePaySecureHash(Request $request): bool
    {
        // Hash key / SECURE_SECRET do OnePAY cấp
        $secureSecret = setting('payment_onepay_hash_code');
    
        if (!$secureSecret) {
            return false;
        }
    
        $data = [];
    
        // 1️⃣ Lấy tất cả tham số vpc_ và user_ (KHÔNG lấy vpc_SecureHash)
        foreach ($request->all() as $key => $value) {
            if (
                ($key !== 'vpc_SecureHash') &&
                ($value !== '') &&
                (strpos($key, 'vpc_') === 0 || strpos($key, 'user_') === 0)
            ) {
                $data[$key] = $value;
            }
        }
    
        // 2️⃣ Sắp xếp key theo alphabet
        ksort($data);
    
        // 3️⃣ Tạo chuỗi dữ liệu: key=value&key=value...
        $hashString = '';
        foreach ($data as $key => $value) {
            $hashString .= $key . '=' . $value . '&';
        }
        $hashString = rtrim($hashString, '&');
    
        // 4️⃣ Tạo hash bằng HMAC-SHA256
        $calculatedHash = strtoupper(
            hash_hmac('SHA256', $hashString, pack('H*', $secureSecret))
        );
    
        // 5️⃣ So sánh hash
        return hash_equals(
            $calculatedHash,
            strtoupper($request->input('vpc_SecureHash'))
        );
    }
        
    public function error(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(route("public.cart"))
            ->withInput()
            ->setMessage(__('Payment failed!'));
    }
}
