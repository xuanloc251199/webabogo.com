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
use RealRashid\SweetAlert\Facades\Alert;

class VillaController extends PublicController
{

    public function addVillaToCart(CartRequest $request)
    {

//        try {
        if (!auth("customer")->check()) {

            return $this
                ->httpResponse()
                ->setError()
                ->setData(['next_url' => route("customer.login")])
                ->setMessage(__('Vui lòng đăng nhập trước khi thêm sản phẩm vào giỏ hàng!'));
        }
        $product = Product::query()->where("type", "villa")->find($request->input('id'));

        if (!$product) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Không tìm thấy villa!'));
        }
        // Handle villa product (existing logic)
        try {
            $startDate = Carbon::createFromFormat("d/m/Y", $request->input('start_date', Carbon::now()->toDateString()));
            $endDate = Carbon::createFromFormat("d/m/Y", $request->input('end_date', Carbon::now()->addDay()->toDateString()));
        } catch (\Exception $e) {
            $startDate = Carbon::parse($request->input('start_date', Carbon::now()->toDateString()));
            $endDate = Carbon::parse($request->input('end_date', Carbon::now()->addDay()->toDateString()));
        }

        $period = CarbonPeriod::create($startDate, $endDate->copy()->subDay());

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        if (empty($dates)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage("Vui lòng chọn lại ngày bắt đầu và ngày kết thúc!");
        }



        $pricesInDay = $this->get_price_days_choose_product_villa($product->id, $startDate, $endDate);

        if (!$pricesInDay || count($pricesInDay) != count($dates)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage("Ngày bạn cho đã hết hàng");
        }
        $adults = MetaBox::getMetaData($product, 'max_adults', true) ?: 1;
        $children = MetaBox::getMetaData($product, 'max_children', true) ?: 0;
        $request->merge(['qty' => 1]);

//Dịch vụ đi kèm
        $services = $product->services->toArray();
        $serviceIds = array_column($services, 'id');

        $servicesData = $request->input("services", []);
        $totalServicePrice = 0;//Tổng tiền dịch vụ
        if (is_array($servicesData)) {
            foreach ($servicesData as $serviceId => $quantity) {
                if (in_array($serviceId, $serviceIds)) {
                    $index = array_search($serviceId, $serviceIds);
                    $services[$index]['quantity'] = $quantity;
                    $totalServicePrice += $services[$index]["price"] * (int)$quantity;
                }
            }
        }

        $request->merge([
            'services' => $services,
            "price_service_other" => $totalServicePrice ?? 0
        ]);

        $addon = MetaBox::getMetaData($product, 'addonFee', true) ?: 0;

        $productPrice = array_sum($pricesInDay);
        //        Ngày book
        $daysBuy = array_keys($pricesInDay);

        $options = $request->input('options', []);
        $options['image'] = $product->image;


        $request->merge([
            'options' => $options,
            "price" => $productPrice,
            "days" => $daysBuy,
            "addOn" => $addon,
            "children_surplus" => 0,
            "priceChildrenSurplus" => 0,
            "adults_number" => $adults,
            "children_number" => $children,
        ]);
        $cartItems = $this->handleAddCart($product, $request);
        $this
            ->httpResponse()->setMessage(__(
                'Added product :product to cart successfully!',
                ['product' => $product->name]
            ));
        $responseData = [
            'status' => true,
            'content' => $cartItems,
        ];
        if ($request->input('checkout')) {
            $token = OrderHelper::getOrderSessionToken();
            $nextUrl = route('public.checkout.information', $token);
            $responseData['next_url'] = $nextUrl;

            if ($request->ajax() && $request->wantsJson()) {
                return $this
                    ->httpResponse()->setData($responseData);
            }

            return $this
                ->httpResponse()
                ->setData($responseData)
                ->setNextUrl($nextUrl);
        }
        return $this
            ->httpResponse()
            ->setData([
                'count' => Cart::instance('cart')->count(),
                'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                'content' => Cart::instance('cart')->content(),
                'cart_content' => null,
                'total_product_cart' => Cart::instance('cart')->rawTotalQuantity(),
                ...$responseData,
            ]);
//        } catch (\Exception $exception) {
//            return $this
//                ->httpResponse()
//                ->setError()
//                ->setMessage($exception->getMessage());
//        }

    }

//    Hiên thị gi theo ngày của villa
    public function getProductPriceByCalendar(
        Request          $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ): BaseHttpResponse
    {
        $product = $productRepository->findById($request->input('product_id'));
        return $response->setData($this->get_data_show_calendar_product($product->id));
    }


//    Cập nht lại ngày nhận và trả phòng
    public function updateCartItem($product_id, $rowId, Request $request)
    {
        $cartItem = Cart::instance('cart')->get($rowId);

        if (!$cartItem) {
            Alert::warning("Bạn chưa đặt villa này!");
            return redirect()->back();
        }

        $condition = [
            'ec_products.id' => $product_id,
            'ec_products.status' => BaseStatusEnum::PUBLISHED,
        ];

        $product = get_products(
            [
                'condition' => $condition,
                'take' => 1,
                'with' => [
                    'slugable',
                ]
            ]
        );
        if (!$product) {
            Alert::warning('Villa không tồn tại!');
            return redirect()->back();
        }

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $period = CarbonPeriod::create($startDate, $endDate->copy()->subDay());

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        if (empty($dates)) {
            Alert::warning("Vui lòng chọn lại khoảng ngày!");
            return redirect()->back();
        }

        $pricesInDay = $this->get_price_days_choose_product_villa($product->id, $startDate, $endDate);

        if (count($pricesInDay) != count($dates)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage("Ngày bạn cho đã hết hàng");
            Alert::warning("Ngày bạn cho đã hết hàng");
            return redirect()->back();
        }

        $cartItem->options["days"] = $dates;
        Cart::instance('cart')->update($cartItem->rowId, [
            'price' => array_sum($pricesInDay),
            "options" => $cartItem->options
        ]);
        Alert::success("Đã thay đổi ngày đặt villa thành công!");
        return redirect()->route("public.cart");
    }

    public function handleAddCart(Product $product, Request $request): array
    {
        if ($product->status != BaseStatusEnum::PUBLISHED) {
            throw new ProductIsNotActivatedYetException();
        }
        $parentProduct = $product->original_product;

        $image = $product->image ?: $parentProduct->image;
        $options = [];
        if ($requestOption = $request->input('options')) {
            $options = OrderHelper::getProductOptionData($requestOption);
        }

        /**
         * Add cart to session
         */
        Cart::instance('cart')->addQuietly(
            $product->getKey(),
            BaseHelper::clean($product->name),
            $request->input('qty', 1),
            $request->input('price'),
            [
                'image' => $image,
                'attributes' => $product->is_variation ? $product->variation_attributes : '',
                'taxRate' => $parentProduct->total_taxes_percentage,
                'taxClasses' => $parentProduct->taxes->pluck('percentage', 'title')->all(),
                'options' => $options,
                'extras' => $request->input('extras', []),
                'sku' => $product->sku,
                'weight' => $product->weight,
                "adults_number" => $request->input("adults_number"),
                "children_number" => $request->input("children_number"),
                "days" => $request->input("days"),
                "services" => $request->input("services"),
                "price_service_other" => $request->input("price_service_other"),
                "children_surplus" => $request->input("children_surplus"),
                "priceChildrenSurplus" => $request->input("priceChildrenSurplus"),
                "addOn" => $request->input("addOn"),
            ]
        );
        return Cart::instance('cart')->content()->toArray();
    }

//Lấy ra danh sách giá của ngày để hiển thị ở calendar
    public function get_data_show_calendar_product($product_id): array
    {
        $startDate = Carbon::now();
        $availableDates = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $product_id)
            ->whereDate('ec_product_attributes.title', ">=", $startDate->format('Y-m-d'))
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.display_layout as attribute_set_display_layout',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.with_storehouse_management',
                'ec_products.stock_status',
                'ec_products.quantity'
            ])
            ->get();
        $dates = [];
        foreach ($availableDates as $item) {
            if ($item->with_storehouse_management == 1) {
                if ($item->quantity > 0) {
                    $dates[] = [
                        'title' => format_price_vietnamese($item->sale_price ?: $item->price),
                        'start' => $item->title,
                    ];
                }
            } else {
                if ($item->stock_status == "in_stock") {
                    $dates[] = [
                        'title' => format_price_vietnamese($item->sale_price ?: $item->price),
                        'start' => $item->title,
                    ];
                }
            }
        }
        return collect($dates)
            ->sortBy('start') // Sắp xếp theo ngày nếu cần
            ->values()->toArray();
    }

//Lấy ra các giá của các ngày đã chọn của villa
    public function get_price_days_choose_product_villa($product_id, $startDate, $endDate, $number_room = 1): array
    {
        $availableDates = ProductVariationItem::query()
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=', 'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->distinct()
            ->where('ec_product_variations.configurable_product_id', $product_id)
            ->whereDate('ec_product_attributes.title', ">=", $startDate)
            ->whereDate('ec_product_attributes.title', "<", $endDate)
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.display_layout as attribute_set_display_layout',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
                'ec_product_variations.id as variation_id',
                'ec_products.price',
                'ec_products.sale_price',
                'ec_products.with_storehouse_management',
                'ec_products.stock_status',
                'ec_products.quantity'
            ])
            ->get();
        $dates = [];
        foreach ($availableDates as $item) {
            if ($item->with_storehouse_management == 1) {
                if ($item->quantity > 0) {
                    $dates[$item->title] = $item->sale_price ?: $item->price;
                }
            } else {
                if ($item->stock_status == "in_stock") {
                    $dates[$item->title] = $item->sale_price ?: $item->price;
                }
            }
        }
        return $dates;
    }
}
