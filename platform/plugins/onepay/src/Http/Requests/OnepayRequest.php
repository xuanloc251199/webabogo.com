<?php

namespace Botble\Onepay\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class OnepayRequest extends Request
{
    public function rules(): array
    {
        return [
            'session_id' => 'required|size:66',
        ];
    }
}
