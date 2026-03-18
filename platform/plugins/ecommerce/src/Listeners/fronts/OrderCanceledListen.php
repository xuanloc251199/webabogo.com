<?php

namespace Botble\Ecommerce\Listeners\fronts;

use Botble\Ecommerce\Events\fronts\OrderCanceledEvent;
use Botble\Ecommerce\Models\Notification;

class OrderCanceledListen
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
    public function handle(OrderCanceledEvent $event): void
    {

        $order = $event->order;
        Notification::create([
            "customer_id" => $order->user_id,
            "action" => "purchase",
            "title" => "Đặt đơn thất bại!",
            "content" => "Đơn đặt của bạn đã bị hủy hoặc thanh toán thất bại",
        ]);
    }
}
