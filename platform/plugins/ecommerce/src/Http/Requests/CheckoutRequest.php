<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'payment_method' => 'required|' . Rule::in(PaymentMethodEnum::values()),
            "address.name" => "required|max:250|min:2",
            "address.email" => "required|string|email|max:250",
            "address.phone" => "required|string",
            "request_invoice" => "nullable|boolean",
        ];
        if ($this->request->has("with_tax_information")) {
            $rules["tax_information.company_name"] = "required|max:250";
            $rules["tax_information.company_address"] = "required|max:250";
            $rules["tax_information.company_tax_code"] = "required|max:250";
            $rules["tax_information.company_email"] = "required|string|email|max:250";
        }
        return $rules;
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        $attributes = [
            "address.name" => "Họ & Tên",
            "payment_method" => "Phương thức thanh toán",
            "address.email" => "Email",
            "address.phone" => "Số điện thoại",
            "request_invoice" => "Yêu cầu xuất hóa đơn",
        ];
        if ($this->request->has("with_tax_information")) {
            $attributes["tax_information.company_name"] = "Tên công ty";
            $attributes["tax_information.company_address"] = "Địa chỉ công ty";
            $attributes["tax_information.company_tax_code"] = "Mã số thuế";
            $attributes["tax_information.company_email"] = "Email công ty";
        }
        return $attributes;
    }
}
