<?php

namespace Botble\Ecommerce\Listeners\fronts;

use Botble\Ecommerce\Events\fronts\PaymentSuccessedEvent;
use Botble\Ecommerce\Models\Notification;

class PaymentSuccessedListen
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
    public function handle(PaymentSuccessedEvent $event): void
    {
        $order = $event->order;
        Notification::create([
            "customer_id" => $order->user_id,
            "action" => "payment",
            "title" => "Thanh toán thành công!",
            "content" => "Thanh toán thành công đơn hàng $order->code",
        ]);
    }
}
