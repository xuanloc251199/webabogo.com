<?php

namespace Botble\Onepay\Class;

class CreateInvoice
{
    private $merchantId;
    private $merchantAccessCode;
    private $merchantHashCode;

    public function __construct($merchantId, $merchantAccessCode, $merchantHashCode)
    {
        $this->merchantId = $merchantId;
        $this->merchantAccessCode = $merchantAccessCode;
        $this->merchantHashCode = $merchantHashCode;
    }

    public function cInvoice($vpc_Amount,$vpc_Customers,$againLink,$vpc_ReturnURL,$vpc_OrderInfo)
    {
        $vpcMerchantTxnRef = "TEST_" . time();
        $merchantParam = [
            "vpc_Version" => "2",
            "vpc_Currency" => "VND",
            "vpc_Command" => "pay",
            "vpc_AccessCode" => $this->merchantAccessCode,
            "vpc_MerchTxnRef" => $vpcMerchantTxnRef,
            "vpc_Merchant" => $this->merchantId,
            "vpc_Locale" => "vn",
            "vpc_ReturnURL" => $vpc_ReturnURL,
            "vpc_OrderInfo" => $vpc_OrderInfo,
            "vpc_Amount" => (int)$vpc_Amount*100,
            "vpc_TicketNo" => request()->ip(),
            "AgainLink" => $againLink,
            "Title" => "PHP VPC 3-Party",
            "vpc_Customer_Phone" => $vpc_Customers["phone"]??null,
            "vpc_Customer_Email" => $vpc_Customers["email"]??null,
            "vpc_Customer_Id" => $vpc_Customers["id"]??null
        ];

        $util = new Util();
        ksort($merchantParam);
        $stringToHash = $util->generateStringToHash($merchantParam);
        $secureHash = $util->generateSecureHash($stringToHash, $this->merchantHashCode);
        // Thêm tham số mới
        $merchantParam['vpc_SecureHash'] = $secureHash;
        return Config::BASE_URL . Config::URL_PREFIX . http_build_query($merchantParam);
        $util->sendHttpRequest($requestUrl);
    }
}
