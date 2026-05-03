<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->user()?->id;

        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('ec_customers', 'email')->ignore($customerId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],

            'facebook_url' => ['nullable', 'string', 'max:191'],
            'instagram_url' => ['nullable', 'string', 'max:191'],
            'youtube_url' => ['nullable', 'string', 'max:191'],
            'tiktok_url' => ['nullable', 'string', 'max:191'],
            'twitter_url' => ['nullable', 'string', 'max:191'],
            'linkedin_url' => ['nullable', 'string', 'max:191'],
            'wechat_url' => ['nullable', 'string', 'max:191'],
        ];
    }
}