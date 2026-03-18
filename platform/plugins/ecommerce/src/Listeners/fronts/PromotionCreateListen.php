<?php

namespace Botble\Ecommerce\Listeners\fronts;

use Botble\Base\Facades\BaseHelper;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Events\fronts\PromotionCreateEvent;
use Botble\Ecommerce\Models\Notification;

class PromotionCreateListen
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
    public function handle(PromotionCreateEvent $event): void
    {

        $discount = $event->discount;
        $isCoupon = $discount->type === DiscountTypeEnum::COUPON;
        $customers = $discount->customers;
        $endDate = $discount->end_date ? "Sử dung trước " . $discount->end_date->format("H:i - d/m/Y") : "";

        if ($customers->count() > 0) {
            foreach ($customers as $customer) {
                Notification::create([
                    "customer_id" => $customer->id,
                    "action" => "promotion",
                    "title" => $isCoupon ? "Sử dụng mã: $discount->code " . "($endDate)" : $discount->title . "($endDate)",
                    "content" => BaseHelper::clean(get_discount_description($discount)),
                ]);
            }
        } else {
            Notification::create([
                "customer_id" => null,
                "action" => "promotion",
                "title" => $isCoupon ? "Sử dụng mã: $discount->code" : $discount->title,
                "content" => BaseHelper::clean(get_discount_description($discount)),
            ]);
        }

    }
}
