<?php

namespace Theme\Abogo\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Theme\Http\Controllers\PublicController;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Http\Requests\CartRequest;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class TourController extends PublicController
{

    public function addTourToCart(Request $request)
    {
        if (!auth("customer")->check()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setData(['next_url' => route("customer.login")])
                ->setMessage(__('Vui lòng đăng nhập trước khi thêm sản phẩm vào giỏ hàng!'));
        }

        $product = Product::query()->where("type", "tour")->find($request->input('id'));

        if (!$product) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Không tìm thấy tour!'));
        }
        $adultVariationId = $request->input('adult_variation_id');
        $childVariationId = $request->input('children_variation_id');
        $tourStartDate = $request->input('start_date');
        try {
            if (empty($tourStartDate)) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(__('Vui lòng chọn ngày khởi hành!'));
            }
            $tourStartDate = Carbon::parse($tourStartDate);
        } catch (\Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Vui lòng chọn ngày khởi hành!'));
        }
        $qty = 1;
        $adultsNumber = $request->input("adults_number", 1);// Số lượng người lớn
        $childrenNumber = $request->input("children_number", 0);// Số lượng trẻ em

        if ($adultsNumber <= 0) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Vui lòng chọn số lượng người lớn!'));
        }
        $availableDates = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $product->id)
            ->whereIn('ec_product_variation_items.variation_id', [$childVariationId, $adultVariationId])
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.display_layout as attribute_set_display_layout',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price'
            ])
            ->get();
        $totalPrice = 0;
        foreach ($availableDates as $variationItem) {
            if (Str::Slug($variationItem->title) == "nguoi-lon") {
                $totalPrice += $adultsNumber * ($variationItem->sale_price ?: $variationItem->price);
            }
            if (Str::Slug($variationItem->title) == "tre-em") {
                $totalPrice += $childrenNumber * ($variationItem->sale_price ?: $variationItem->price);
            }
        }

        $cartItem = Cart::instance('cart')->content()->where('id', $product->id)->first();
        if ($cartItem) {
            Cart::instance('cart')->update($cartItem->rowId, [
                'qty' => $cartItem->qty,
                'price' => $totalPrice,
                "options" => [
                    'image' => $product->image,
                    "adults_number" => $adultsNumber,
                    "children_number" => $childrenNumber,
                    "start_date" => $tourStartDate
                ]
            ]);

        } else {
            $cartItem = Cart::instance('cart')->add(
                $product->getKey(),
                BaseHelper::clean($product->name),
                $qty,
                $totalPrice,
                [
                    'image' => $product->image,
                    "adults_number" => $adultsNumber,
                    "children_number" => $childrenNumber,
                    "start_date" => $tourStartDate
                ]);
        }

        $responseData = [
            'status' => true,
            'content' => Cart::instance('cart')->content(),
            'total_product_cart' => Cart::instance('cart')->rawTotalQuantity(),
        ];
        if ($request->input('checkout')) {
            $token = OrderHelper::getOrderSessionToken();
            $nextUrl = route('public.checkout.information', $token);
            $responseData['next_url'] = $nextUrl;
            return $this
                ->httpResponse()->setData($responseData);

        }
        return $this
            ->httpResponse()
            ->setData($responseData)
            ->setMessage(__('Đã thêm tour vào giỏ hàng!'));
    }
}
