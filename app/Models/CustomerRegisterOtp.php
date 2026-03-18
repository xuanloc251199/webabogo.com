<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRegisterOtp extends Model
{
    protected $table = 'customer_register_otps';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password_hash',
        'otp',
        'expires_at',
        'resend_count',
        'last_sent_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];
}