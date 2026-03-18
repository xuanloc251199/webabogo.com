<?php

namespace Theme\Abogo\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Blog\Models\Post;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Ecommerce\Exceptions\ProductIsNotActivatedYetException;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Models\Review;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Place\Models\Place;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Services\Models\Services;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Http\Controllers\PublicController;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Http\Requests\CartRequest;
use Illuminate\Support\Str;
use PharIo\Version\Exception;
use RealRashid\SweetAlert\Facades\Alert;

class AbogoController extends PublicController
{
    public function getIndex()
    {
        return parent::getIndex();
    }

    public function getView(?string $key = null, string $prefix = '')
    {
        return parent::getView($key);
    }

    public function getSiteMapIndex(string $key = null, string $extension = 'xml')
    {
        return parent::getSiteMapIndex();
    }

    public function getReviews(Request $request)
    {
        Theme::addBodyAttributes(['id' => 'page-reviews']);
        SeoHelper::setTitle("Reviews");
        Theme::breadcrumb()->add("Reviews");
        $reviews = Review::query()
            ->wherePublished()
            ->orderByDesc('star')
            ->orderByDesc('id')
            ->paginate(15);
        $products = Product::query()->where("is_variation", 0)->orderByDesc("name")->pluck("name", "id")->toArray();
        return Theme::scope('reviews', compact("reviews", "products"))->render();
    }

    public function getOrdersHistory()
    {
        SeoHelper::setTitle("Quản lý đơn hàng");
        Theme::breadcrumb()->add("Quản lý đơn hàng");
        $data["orders"] = auth("customer")->user()->orders()->orderByDesc("id")->paginate(5);

        return Theme::scope(
            'ecommerce.orders.history', $data)->render();
    }

    public function getFilterCategory(Request $request): ?string
    {
        $categoryId = $request->input('category_id');
        if ($categoryId === 'all') {
            $posts = get_recent_posts(8);
        } else {
            $posts = get_posts_by_category($categoryId);
        }

        return Theme::partial('shortcodes.includes.category-filter-item', compact('posts'));
    }

    public function getProductOverview(
        Request $request,
    )
    {
        Theme::breadcrumb()->add(__('Product Overview'), route('public.theme.get-product-overview'));
        SeoHelper::setTitle(__('Product Overview'))->setDescription(__('Product Overview'));
        return Theme::scope(
            'ecommerce.product-overview',
            [],
            null)->render();
    }

    public function isValidDate($dateString, $format = 'Y-m-d')
    {
        try {
            $d = Carbon::createFromFormat($format, $dateString);
            return $d && $d->format($format) === $dateString;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProductPriceByCalendar(
        Request          $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ): BaseHttpResponse
    {
        $product = $productRepository->findById($request->input('product_id'));
        $events = $this->get_data_show_calendar_product($product->id);
        return $response->setData($events);
    }

//    Change Date Buy Now - chon lại ngày trong product để mua lại
    public function reschedule($product_id, $rowId, Request $request, BaseHttpResponse $response)
    {
        $cartItem = Cart::instance('cart')->get($rowId);
        if (!$cartItem) {
            Alert::warning("Bạn chưa đặt phòng này!");
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
                ],
            ]
        );
        if (!$product) {
            Alert::warning("Sản phẩm không tôn tại!");
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

        $pricesInDay = $this->getPriceInDaysAndProductVariations($dates, $product);

        $children_surplus = $cartItem->options->get('children_surplus', 0);
        $priceChildrenSurplus = 0;
        if ($children_surplus > 0) {
            $children_surplus_fee = MetaBox::getMetaData($product, 'children_surplus_fee', true) ?: 0;
            if ($children_surplus_fee > 0) {
                $priceChildrenSurplus = $children_surplus * $children_surplus_fee * count($pricesInDay);
            }
        }
        $cartItem->options["days"] = $dates;
        $cartItem->options["priceChildrenSurplus"] = $priceChildrenSurplus;

        Cart::instance('cart')->update($cartItem->rowId, [
            'price' => array_sum($pricesInDay),
            "options" => $cartItem->options
        ]);
        Alert::success("Đã thay đổi ngày đặt thành công!");
        return redirect()->route("public.cart");
    }

    public function getPricereschedule($product_id, Request $request, BaseHttpResponse $response)
    {
        if (!auth("customer")->check()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Vui lòng đăng nhập trước khi thêm sản phẩm vào giỏ hàng!'));
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
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('This product is out of stock or not exists!'));
        }

        if ($product->isOutOfStock()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __(
                        'Product :product is out of stock!',
                        ['product' => $product->name]
                    )
                );
        }
        $startDate = Carbon::parse($request->input('start'));
        $endDate = $startDate->copy()->addDay();
        if (!$request->input('end')) {
            $dates = [$startDate->format('Y-m-d')];
        } else {
            $endDate = Carbon::parse($request->input('end'));

            $period = CarbonPeriod::create($startDate, $endDate->copy()->subDay());

            $dates = [];
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }
        }


        if (empty($dates)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage("Vui lòng chọn lại khoảng ngày!");
        }


        $qty = 1;
        $rowId = $request->get("rowId");
        if (!empty($rowId)) {
            $cartItem = Cart::instance('cart')->get($rowId);
            if (!$cartItem) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage("Bạn chưa đặt phòng này!");
            }
            $qty = $cartItem->qty;
        }

        $pricesInDay = get_price_chose_calendar_product($product->id, $startDate->format("Y-m-d"), $endDate->format("Y-m-d"));

        if (count($pricesInDay) != count($dates)) {
            return $response->setError()
                ->setData(["totalPrice" => 0])
                ->setMessage("Vui lòng chọn lại ngày! Vì Ngày bạn chọn có ngày đã hết phòng");
        }
//        $pricesInDay = $this->getPriceInDaysAndProductVariations($dates, $product);

        $price = array_sum($pricesInDay) * $qty;
        return $response->setData(["totalPrice" => $price]);

    }

    public function getProductBlogs(
        Request    $request,
        int|string $id,
    )
    {
        $blog = Post::query()
            ->select('id', 'name', 'description', 'image')
            ->findOrFail($id);
        if (!$blog) {
            return null;
        }

        return Theme::partial(
            'product-blog-item',
            compact('blog')
        );
    }

    public function ajaxSuggestSearch(Request $request, GetProductService $productService)
    {
        $key = $request->get("q");
        $place_id = $request->get("place");
        $place = Place::find($place_id);
        if ($place) {
            $products = $place->products();

        } else {
            $products = Product::query();
        }

        $products = $products
            ->where("name", "like", "%$key%")
            ->where("status", BaseStatusEnum::PUBLISHED)
            ->where("is_variation", 0)
            ->orderByDesc("id")
            ->limit(15)
            ->with("slugable")
            ->get();
        return Theme::partial(
            'result_search',
            compact('products')
        );
    }

//    Ajax tìm phòng trong chi tiết sản phẩm
    public function ajaxGetRoomsProduct($product_id, Request $request, BaseHttpResponse $response)
    {
        $product = Product::query()
            ->where("status", BaseStatusEnum::PUBLISHED)
            ->find($product_id);

        if (!$product) {
            return $response
                ->setError()
                ->setMessage("Không tìm thấy sản phẩm!");
        }
        $start_date = $request->input("startDate");
        $end_date = $request->input("endDate");

        $start_date = Carbon::createFromFormat("d/m/Y", $start_date);
        $end_date = Carbon::createFromFormat("d/m/Y", $end_date);

        $period = CarbonPeriod::create($start_date, $end_date->copy()->subDay());
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        if (empty($dates)) {
            return $response
                ->setError()
                ->setMessage("Vui lòng chọn lại khoảng ngày!");
        }
        $adults = $request->input("adults");
        $children = $request->input("children");

        $product_group_id = $request->input("room_group");//id product đang tìm kiếm
        if ($adults <= 0) {
            return $response
                ->setError()
                ->setMessage("Vui lòng chọn số người lớn!");
        }


        $products_group = $product->groupedProduct()
            ->where("status", BaseStatusEnum::PUBLISHED)
            ->with(["variations", "variations.variationItems", "variations.variationItems.attribute"])
            ->get();
        $data = [];
        $product_group = null;
        foreach ($products_group as $group) {
            if ($product_group_id == $group->id) {
                $product_group = $group;
            }
//        Tính tổng số phòng cần đặt
            $maxAdults = (int)MetaBox::getMetaData($group, 'max_adults', true);
            if ($maxAdults <= 0) {
                $maxAdults = 1;
            }
            $number_room = ceil($adults / $maxAdults);
//            $prices = $this->getTotalPriceAllDays($dates, $group);

            $itemCart = Cart::instance('cart')->content()->where("id", $group->id)->first();
            if ($itemCart) {
                $total_room = $itemCart->qty + $number_room;
            } else {
                $total_room = $number_room;
            }
            $prices = $this->get_price_days_choose_product_hotel($group->id, $start_date, $end_date, $total_room);

            if (count($prices) != count($dates)) {
                continue;
            }
            $data[$group->id] = ["prices" => $prices];

            $maxChildren = (int)MetaBox::getMetaData($group, 'max_children', true);
            $children_surplus_fee = (int)MetaBox::getMetaData($group, 'children_surplus_fee', true);//Số tiền phụ thu cho 1 trẻ em thừa
            $addon = (int)MetaBox::getMetaData($group, 'addonFee', true);


            $totalPrice = array_sum($prices) * $number_room + $addon;
            $children_surplus = $children - ($number_room * $maxChildren);
            if ($children_surplus > 0) {
                $totalPrice += $children_surplus * $children_surplus_fee * count($prices);
            }
            if ($request->has("services")) {
                $services = $product->services->toArray();
                $serviceIds = array_column($services, 'id');
                $servicesData = $request->input("services", []); //Dịch vụ đi kèm
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
                $totalPrice += $totalServicePrice;
            }


            $data[$group->id]["addon"] = $addon;
            $data[$group->id]["totalPrice"] = $totalPrice;
            $data[$group->id]["number_room"] = $number_room;
            $data[$group->id]["maxAdults"] = $maxAdults * $number_room;
            $data[$group->id]["maxChildren"] = $maxChildren * $number_room;
            $data[$group->id]["children_surplus"] = $children_surplus;
            $data[$group->id]["children_surplus_fee"] = $children_surplus_fee;
        }
        if (!$product_group) {
            return $response
                ->setError()
                ->setMessage("Vui lòng chọn Hạng phòng!");
        }

//        if ($product->isOutOfStock()) {
//            return $response
//                ->setError()
//                ->setMessage(
//                    __(
//                        'Product :product is out of stock!',
//                        ['product' => $product_group->name ?: $product->name]
//                    )
//                );
//        }
        return $response
            ->setData([
                "detail_price" => Theme::partial(
                    'ecommerce.products.detail-price',
                    compact("data", "product", "product_group")
                ),
                "products_grouped" => Theme::partial(
                    'ecommerce.products.products-grouped',
                    compact("data", "product")
                ),

                "detail_total_price" => Theme::partial(
                    'ecommerce.products.detail-total-price',
                    compact("data", "product", "product_group")),
            ]);

    }

//    Lấy giá của ngày hiện tại cho một sản phẩm
    public function getCurrentDatePrice($product, $date = null, $number_room = 1)
    {
        if (!$date) {
            $date = Carbon::now();
        }

//        $prices = $this->getPriceInDaysAndProductVariations($dates, $product);

        $prices = $this->get_price_days_choose_product_hotel($product->id, $date, $date->copy()->addDay(), $number_room);

        if (isset($prices[$date->toDateString()])) {
            return $prices[$date->toDateString()];
        }
        // Fallback to regular price if no date-specific price found
        return null;
    }


//    Lấy ra danh sách giá theo ngày của khách sạn và danh sách các biến thể của product đạt điều kiện
    private function getPriceInDaysAndProductVariations($dates, $product_group): array
    {
        $prices = [];

        $price_product_group = $product_group->sale_price ?: $product_group->price;
        $variations = $product_group->variations;
        $attributes = [];
//        $productVariations = [];
        if ($variations->count() > 0) {

            foreach ($variations as $variation) {
                $variationItems = $variation->variationItems;
                $productVariation = $variation->product;
                $priceProductVariation = $productVariation->sale_price ?: $productVariation->price;

                foreach ($variationItems as $variationItem) {
                    $attribute = $variationItem->attribute;
                    if (!empty($attribute) && in_array($attribute->title, $dates)) {
                        $attributes[] = $attribute->title;
//                    $productVariations[] = $productVariation;
                        if (is_object($productVariation)) {
                            $prices[$attribute->title] = $priceProductVariation;
                        } else {
                            $prices[$attribute->title] = $price_product_group;
                        }
                    }
                }
            }

//        Lấy ra các ngày còn lại chưa được set giá
            $args = array_diff($dates, $attributes);
            foreach ($args as $date) {
                $prices[$date] = $price_product_group;
//            $prd_ids[] = $product_group->id;
            }
        } else {
            foreach ($dates as $date) {
                $prices[$date] = $price_product_group;
            }
        }
        ksort($prices);
        return $prices;
    }

//    Lấy ra danh sách giá theo ngày của khách sạn và danh sách các biến thể của product đạt điều kiện
    private function getTotalPriceAllDays($dates, $product_group)
    {
        $prices = [];

        $price_product_group = $product_group->sale_price ?: $product_group->price;
        $variations = $product_group->variations;
        $attributes = [];
//        $productVariations = [];
        foreach ($variations as $variation) {
            $variationItems = $variation->variationItems;
            $productVariation = $variation->product;
            $priceProductVariation = $productVariation->sale_price ?: $productVariation->price;
            foreach ($variationItems as $variationItem) {
                $attribute = $variationItem->attribute;
                if (!empty($attribute) && in_array($attribute->title, $dates)) {
                    $attributes[] = $attribute->title;
//                    $productVariations[] = $productVariation;
                    if (is_object($productVariation)) {
                        $prices[$attribute->title] = $priceProductVariation;
                    } else {
                        $prices[$attribute->title] = $price_product_group;
                    }
                }
            }
        }

//        Lấy ra các ngày còn lại chưa được set giá
        $args = array_diff($dates, $attributes);
        foreach ($args as $date) {
            $prices[$date] = $price_product_group;
        }
        ksort($prices);
        return $prices;
    }


    public function addHotelToCart(CartRequest $request)
    {
//        try {
        if (!auth("customer")->check()) {

            return $this
                ->httpResponse()
                ->setError()
                ->setData(['next_url' => route("customer.login")])
                ->setMessage(__('Vui lòng đăng nhập trước khi thêm sản phẩm vào giỏ hàng!'));
        }
        $originProduct = Product::query()->where("type", "hotel")->find($request->input('origin_product_id'));
        if ($originProduct) {
            $product = $originProduct->groupedProduct()->find($request->input('id'));
        } else {
            $product = Product::query()->find($request->input('id'));
        }
        if (!$product) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Không tìm thấy phòng trong Hotel!'));
        }
//        if ($product->isOutOfStock()) {
//            return $this
//                ->httpResponse()
//                ->setError()
//                ->setMessage(
//                    __(
//                        'Product :product is out of stock!',
//                        ['product' => $product->name]
//                    )
//                );
//        }

        // Handle hotel product (existing logic)
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

        $adults = $request->input("adults", 1);
        $children = $request->input("children", 0);

        if ($adults <= 0) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Phải có ít nhất 1 người lớn!'));
        }

        $maxAdults = MetaBox::getMetaData($product, 'max_adults', true) ?: 1;


//Tính toán số phòng, giá phòng
        if ($maxAdults <= 0) {
            $maxAdults = 1;
        }
        $number_room = 1;
        if ($adults > $maxAdults) {
            $number_room = ceil($adults / $maxAdults);//Số lượng phòng câ đặt
        }
        $request->merge(['qty' => $number_room]);
//Dịch vụ đi kèm
        if ($originProduct) {
            $services = $originProduct->services->toArray();
        } else {
            $services = $product->services->toArray();
        }

        $serviceIds = array_column($services, 'id');

        $servicesData = $request->input("services", []); //Dịch vụ đi kèm
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
//                Lấy giá theo ngày

        $itemCart = Cart::instance('cart')->content()->where("id", $product->id)->first();

        if ($itemCart) {
            $total_room = $itemCart->qty + $number_room;
        } else {
            $total_room = $number_room;
        }
        $pricesInDay = $this->get_price_days_choose_product_hotel($product->id, $startDate, $endDate, $total_room);

        if (!$pricesInDay) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Hết phòng hoặc không đủ số lượng phòng bạn yêu cầu!'));
        }
        $maxChildren = MetaBox::getMetaData($product, 'max_children', true) ?: 0;
        $children_surplus_fee = MetaBox::getMetaData($product, 'children_surplus_fee', true) ?: 0;
        $addon = MetaBox::getMetaData($product, 'addonFee', true) ?: 0;

        $productPrice = array_sum($pricesInDay);
        //        Tổng số trẻ em thừa
        $children_surplus = $children - ($number_room * $maxChildren);
        $priceChildrenSurplus = 0;
        if ($children_surplus > 0) {
            //        Tổng phụ thu thêm trẻ em thừa  = sô trẻ thừa * phí phụ thu * số ngày book
            $priceChildrenSurplus = $children_surplus * $children_surplus_fee * count($pricesInDay);
        }
        //        Ngày book
        $daysBuy = array_keys($pricesInDay);

        $options = $request->input('options', []);
        $options['image'] = $product->image;
        $request->merge([
            'options' => $options,
            "price" => $productPrice,
            "days" => $daysBuy,
            "addOn" => $addon,
            "children_surplus" => $children_surplus,
            "priceChildrenSurplus" => $priceChildrenSurplus,
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

    public function updateCartService(Request $request)
    {
        $cartId = $request->input('cart_id');
        $services = $request->input('services', []);

        $cartContent = Cart::instance('cart')->content();
        $cartItem = $cartContent->get($cartId);

        if (!$cartItem) {
            return $this->httpResponse()
                ->setError()
                ->setMessage('Không tìm thấy sản phẩm trong giỏ hàng!');
        }

        // Get current services from cart item
        $currentServices = $cartItem->options->services ?? [];
        $servicesUpdated = [];
        $totalServicePrice = 0;

        // Update quantities for all services
        foreach ($currentServices as $service) {
            $serviceId = $service['id'];
            $quantity = isset($services[$serviceId]) ? (int)$services[$serviceId] : 0;

            $service['quantity'] = $quantity;
            $servicesUpdated[] = $service;
            $totalServicePrice += $service['price'] * $quantity;
        }

        // Update cart item options
        $options = $cartItem->options->toArray();
        $options['services'] = $servicesUpdated;
        $options['price_service_other'] = $totalServicePrice;

        // Update cart item
        Cart::instance('cart')->update($cartId, ['options' => $options]);

        return $this->httpResponse()
            ->setMessage('Cập nhật số lượng dịch vụ thành công!')
            ->setData([
                'subtotal' => format_price(Cart::instance('cart')->rawSubTotal()),
                'total' => format_price(Cart::instance('cart')->rawTotal())
            ]);
    }

    /**
     * Handle tour product add to cart
     *
     * @param Product $product
     * @param Request $request
     * @return array
     */
    private function handleTourAddToCart($product, $request)
    {
        $adultVariationId = $request->input('adult_variation_id');
        $childVariationId = $request->input('child_variation_id');
        $variationId = $request->input('variation_id'); // fallback
        $tourDate = $request->input('tour_date');

        // Validation
        if ((!$adultVariationId && !$variationId) || !$tourDate) {
            return [
                'error' => true,
                'message' => "Vui lòng chọn ngày khởi hành!"
            ];
        }

        // Get adult and child prices
        $adultPrice = 0;
        $childPrice = 0;
        $adultVariation = null;
        $childVariation = null;

        // Get adult variation
        $adultVariation = ProductVariation::query()
            ->where('id', $adultVariationId ?: $variationId)
            ->where('configurable_product_id', $product->id)
            ->first();

        if ($adultVariation && $adultVariation->product) {
            $adultPrice = $adultVariation->product->front_sale_price ?: $adultVariation->product->price;
        }

        // Get child variation if exists
        if ($childVariationId) {
            $childVariation = ProductVariation::query()
                ->where('id', $childVariationId)
                ->where('configurable_product_id', $product->id)
                ->first();

            if ($childVariation && $childVariation->product) {
                $childPrice = $childVariation->product->front_sale_price ?: $childVariation->product->price;
            }
        }

        if ($adultPrice <= 0) {
            return [
                'error' => true,
                'message' => "Ngày khởi hành không hợp lệ!"
            ];
        }

        // Get quantities
        $adults = $request->input("adults_number", 1);
        $children = $request->input("children_number", 0);

        // Calculate total price
        $productPrice = ($adultPrice * $adults) + ($childPrice * $children);

        return [
            'error' => false,
            'product' => $adultVariation->product,
            'dates' => [$tourDate],
            'productPrice' => $productPrice,
            'adults' => $adults,
            'children' => $children,
            'adultPrice' => $adultPrice,
            'childPrice' => $childPrice,
            'tourDate' => $tourDate,
            'children_surplus' => 0,
            'priceChildrenSurplus' => 0,
            'addon' => 0
        ];
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
    public function get_price_days_choose_product_hotel($product_id, $startDate, $endDate, $number_room): array
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
                if ($item->quantity > 0 && $item->quantity >= $number_room) {
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
