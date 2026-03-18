<?php

namespace Botble\Ecommerce\Listeners\fronts;

use Botble\Ecommerce\Models\Notification;

class ChangePasswordListen
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Notification::create([
            "customer_id" => $event->customer_id,
            "action" => "account",
            "title" => "Đô mật kẩu!",
            "content" => "Bạn đã đổi mật khẩu thành công!",
        ]);
    }
}
