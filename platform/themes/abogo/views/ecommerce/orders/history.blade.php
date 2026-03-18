<style>
    .total-price-item {
        font-size: 26px;
    }
    .services-toggle-history{
        cursor: pointer;
    }
</style>
<section data-bb-toggle="cart-content" class="cart-area py-4">
    <div class="container bg-white rounded-20 p-4">

        <!-- Page Title -->
        <h2 class="fw-bold mb-4">Quản lý đơn hàng</h2>
        <div class="row">
            @foreach($orders as $order)
                @foreach($order->products as $product)
                    @php
                        $days = $product->options["days"]??[];
                        $dateStr = $days && count($days)>1 ? Carbon\Carbon::parse(reset($days))->format("d/m/Y")." - ". Carbon\Carbon::parse(end($days))->format("d/m/Y"): Carbon\Carbon::parse(reset($days))->format("d/m/Y")." - ".Carbon\Carbon::parse(reset($days))->addDay()->format("d/m/Y");
                        $services = $product->options["services"]??[];
                        $filteredServices = array_filter($services, function ($service) {
                            return isset($service["quantity"]) && $service["quantity"]>0;
                        });
                        $totalPrice = $product->qty * $product->price + ($product->options["addOn"]??0) + ($product->options["priceChildrenSurplus"]??0) + ($product->options["price_service_other"]??0);
                    @endphp
                    <div class="col-12">
                        <div class="cart-item bg-white rounded-3 p-3 mb-3 position-relative">
                            <div class="row">
                                <!-- Product Image -->
                                <div class="col-md-2 col-12">
                                    <a href="{{$product->product->url}}">
                                        <img
                                            src="{{ RvMedia::getImageUrl($product->product_image, null,false,RvMedia::getDefaultImage()) }}"
                                            alt="{{$product->product_name}}" class="rounded-10"
                                            style="min-width:140px;width:100%;height:100px;aspect-ratio: 1; object-fit: cover;">
                                    </a>
                                </div>

                                <!-- Product Details -->
                                <div class="col-md-10 col-12 pt-3 pt-md-0">
                                    <div
                                        class="cart-item-details d-flex flex-md-row flex-column justify-content-between">
                                        <div class="cart-item-details-info">
                                            <!-- Product Name -->
                                            <h6 class="fw-bold mb-1">
                                                <a href="{{$product->product->url}}"
                                                   class="text-decoration-none text-dark d-block pe-5">
                                                    {{$product->product_name}}
                                                </a>
                                            </h6>
                                            <!-- Basic Info Row -->
                                            <div class="basic-info d-flex  gap-3">
                                                <span>Số lượng người: {{$product->options["adults_number"]??0}} người lớn, {{$product->options["children_number"]??0}} trẻ em</span>

                                                <span class="text-black d-block">
                                                    Ngày: {{ $dateStr }}
                                                </span>

                                            </div>
                                            <div>
                                                <span class="text-black d-block">Số phòng: {{$product->qty}}</span>
                                            </div>
                                            @if($filteredServices)
                                                <!-- Services Toggle -->
                                                <div class="services-toggle-history mt-2">
                                                    <span class="fw-medium">
                                                        Dịch vụ đi kèm
                                                        <i class="fas fa-chevron-down ms-1 toggle-icon"
                                                           aria-hidden="true"></i>
                                                    </span>
                                                </div>

                                                <!-- Services Details (Collapsible) -->
                                                <div class="services-info-history mt-2" style="display: none">
                                                    <div class="pt-2">
                                                        @foreach($filteredServices as $service)
                                                            <div
                                                                class="d-flex justify-content-between align-items-center py-1">
                                                                <span>{{$service["name"]}}(x{{$service["quantity"]}}
                                                                    )</span>
                                                                <span>{{format_price($service["price"])}}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div>
                                                <span class="text-black d-block">Trạng thái: {{Botble\Ecommerce\Enums\OrderStatusEnum::getLabel($order->status)}}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Price Info -->

                                        <div class="d-flex align-items-center gap-2">
                                            Tổng giá trị:
                                            <span class="total-price-item fw-bold">{{format_price($totalPrice)}}</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
            {!! $orders->links(Theme::getThemeNamespace('partials.custom-pagination')) !!}
        </div>
    </div>
</section>
