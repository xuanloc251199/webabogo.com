<?php

namespace Botble\Onepay;

use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('Onepays');
        Schema::dropIfExists('Onepays_translations');
        Setting::delete([
            'payment_onepay_payment_type',
            'payment_onepay_name',
            'payment_onepay_description',
            'payment_onepay_client_id',
            'payment_onepay_secret',
            'payment_onepay_status',
        ]);
    }
}
