<div class="section-2">
    <div class="container py-4 ">
        <div class="bg-white p-3 rounded-10">
            <h3 class="mb-3 fw-bold">Ưu đãi hôm nay</h3>
            <div class="row desktop-hidden">
                @foreach($flashSales as $item)
                    @foreach ($item->products as $product)
                        <div class="col-md-3">
                            <div class="product-item text-left pt-3 bg-white rounded-20">
                                <div class="row">
                                    <div class="col-md-12 col-4">
                                        <img src="{{ RvMedia::getImageUrl($product->image, null, false, RvMedia::getDefaultImage()) }}" class="img-fluid rounded-20"
                                             alt="{{ $product->name }}">
                                    </div>
                                    <div class="col-md-12 col-8">
                                        <h5 class="mt-2 fw-semibold fs-18 fw-600"><a href="{{ $product->url }}" class="text-decoration-none text-dark">{{ $product->name }}</a></h5>
                                        <p class="text-muted mb-1 fs-16 fw-300">Đà Nẵng, Việt Nam</p>
                                        <p class="mb-1 mt-2 fs-18 fw-600 ">
                                            @php
                                                $currentDatePrice = get_current_date_price($product);
                                            @endphp
                                            {{ format_price($currentDatePrice) }}
                                        </p>
                                    </div>
                                    <div class="col-md-12 col-4 desktop-hidden">
                                    </div>
                                    <div class="col-md-12 col-8 desktop-hidden">
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div class="swiper flash-sale-swiper mobile-hidden">
                <div class="swiper-wrapper">
                    @foreach($flashSales as $item)
                        @foreach ($item->products as $product)
                            <div class="swiper-slide">
                                <div class="product-item text-left pt-3 bg-white rounded-20">
                                    <div class="row">
                                        <div class="col-md-12 col-4">
                                            <img src="{{ RvMedia::getImageUrl($product->image, null, false, RvMedia::getDefaultImage()) }}"
                                                 class="img-fluid rounded-20 w-100"
                                                 alt="{{ $product->name }}">
                                        </div>
                                        <div class="col-md-12 col-8">
                                            <h5 class="mt-2 fw-semibold fs-18 fw-600">
                                                <a href="{{ $product->url }}" class="text-decoration-none text-dark">{{ $product->name }}</a>
                                            </h5>
                                            <p class="text-muted mb-1 fs-16 fw-300">Đà Nẵng, Việt Nam</p>
                                            <p class="mb-1 mt-2 fs-18 fw-600">
                                                @php
                                                    $currentDatePrice = get_current_date_price($product);
                                                @endphp
                                                {{ format_price($currentDatePrice) }}
                                            </p>
                                        </div>
                                        <div class="col-md-12 col-4 desktop-hidden"></div>
                                        <div class="col-md-12 col-8 desktop-hidden"><hr></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>
