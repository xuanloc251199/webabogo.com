<?php

namespace Botble\Ecommerce\Listeners\fronts;

use Botble\Ecommerce\Events\fronts\OrderCompletedEvent;
use Botble\Ecommerce\Models\Notification;

class OrderCompletedListen
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
    public function handle(OrderCompletedEvent $event): void
    {

        $order = $event->order;
        Notification::create([
            "customer_id" => $order->user_id,
            "action" => "purchase",
            "title" => "Đặt đơn hoàn tất!",
            "content" => "Đơn đặt của bạn đã được gửi đến Abogo.Vui lòng đợi nhân viên liên hệ lại với bạn!",
        ]);
    }
}
