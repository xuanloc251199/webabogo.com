@php
    use Botble\Ecommerce\Facades\EcommerceHelper;
    $logo = get_ecommerce_setting('company_logo_for_invoicing') ?: (theme_option(
            'logo_in_invoices'
        ) ?: theme_option('logo'));

        $companyName = get_ecommerce_setting('company_name_for_invoicing') ?: get_ecommerce_setting('store_name');

        $companyAddress = get_ecommerce_setting('company_address_for_invoicing');

        $country = EcommerceHelper::getCountryNameById(get_ecommerce_setting('company_country_for_invoicing', get_ecommerce_setting('store_country')));
        $state = get_ecommerce_setting('company_state_for_invoicing', get_ecommerce_setting('store_state'));
        $city = get_ecommerce_setting('company_city_for_invoicing', get_ecommerce_setting('store_city'));

        if (! $companyAddress) {
            $companyAddress = implode(', ', array_filter([
                get_ecommerce_setting('company_address_for_invoicing', get_ecommerce_setting('store_address')),
                $city,
                $state,
                $country,
            ]));
        }

        $companyPhone = get_ecommerce_setting('company_phone_for_invoicing') ?: get_ecommerce_setting('store_phone');
        $companyEmail = get_ecommerce_setting('company_email_for_invoicing') ?: get_ecommerce_setting('store_email');

@endphp
    <!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Biên nhận</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <style>
        :root {
            --primary-color: #0c2f78;
            --text-color: #00072b;
            --border-color: #6d6d6d;
        }

        body {
            font-family: "Be Vietnam Pro", sans-serif;
            background-color: #f8f9fa;
            color: var(--text-color);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        .invoice-container {
            max-width: 700px;
            margin: auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .invoice-header .logo img {
            max-width: 60%;
        }

        .invoice-header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
            letter-spacing: 2px;
            border-bottom: 1.5px solid var(--border-color);
        }

        .invoice-header p {
            font-size: 18px;
            margin: 6px 0;
            color: #00072b;
        }

        .section {
            margin-bottom: 30px;
            border-bottom: 1.5px solid var(--border-color);
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #000;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }

        .info-item .label {
            display: flex;
            align-items: center;
            color: var(--text-color);
            font-weight: 500;
        }

        .info-item .value {
            font-weight: 500;
            text-align: right;
        }

        .icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background-color: #454545;
            border-radius: 8px;
            margin-right: 12px;
        }

        .icon-wrapper svg {
            width: 20px;
            height: 20px;
            opacity: 0.7;
            color: #fff;
        }

        .tour-item {
            display: flex;
            margin-bottom: 20px;
        }

        .tour-item img.tour-image {
            width: 200px;
            height: 115px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 20px;
        }

        .tour-item .tour-details {
            flex: 1;
        }

        .tour-item .tour-name {
            font-weight: 600;
            color: #000;
            font-size: 15px;
        }

        .tour-item .tour-meta {
            font-size: 12px;
            color: #111;
            margin: 4px 0;
        }

        .tour-item .tour-price {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 16px;
        }

        .policy-section ul {
            padding-left: 20px;
            margin-top: 15px;
        }

        .policy-section ul li {
            margin-bottom: 10px;
        }

        .policy-title {
            font-weight: 600;
            color: #000;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .invoice-footer {
            margin-top: 40px;
        }

        .signature p {
            margin: 0;
            line-height: 1.8;
        }

        .signature .sincerely {
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 18px;
        }

        .signature .name {
            font-weight: 700;
            font-size: 16px;
            color: var(--text-color);
            margin-bottom: 25px;
        }

        .contact-details p {
            font-weight: 500;
            font-size: 16px;
        }

        .company-name p {
            font-weight: 700;
            font-size: 18px;
        }

        .signature .contact-info {
            display: flex;
            gap: 60px;
        }

        .signature .company-name {
            font-weight: 600;
        }

        .footer-note {
            text-align: center;
            font-size: 14px;
            color: #333;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1.5px solid var(--border-color);
            font-weight: 500;
        }

        .company-address {
            text-align: center;
            margin-top: 15px;
            font-weight: 600;
        }

        .company-address p {
            margin: 4px 0;
        }

        @media (max-width: 768px) {
            * {
            }

            .contact-details p {
                font-size: 14px;
            }

            .signature .contact-info {
                gap: 20px;
            }

            .section {
                margin-bottom: 20px;
            }

            .tour-name {
                font-size: 14px !important;
            }

            .tour-item img.tour-image {
                width: 120px;
                height: 90px;
                margin-right: 10px;
            }

            .info-item * {
                font-size: 13px;
            }

            .icon-wrapper {
                width: 28px;
                height: 28px;
            }

            .invoice-header p {
                font-size: 16px;
            }

            .invoice-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <header class="invoice-header">
        <div class="logo">
            <img src="{{ RvMedia::getImageUrl($logo, null,false,RvMedia::getDefaultImage()) }}">
        </div>
        <h1>BIÊN NHẬN</h1>
        <p>Kính gửi: {{$order->userName}}</p>
        <p>
            Cảm ơn {{$order->userName}}! chúng tôi xin xác nhận đặt dịch vụ như thông tin
            dưới đây
        </p>
    </header>

    <main>
        @foreach($order->products as $orderProduct)
            @php
                $product = $orderProduct->product;
                $parentProduct = $product->productsGroupedParent()->first();
                $nameHotel = $orderProduct->product_name;
                $address = $product->address;
                if ($parentProduct){
                    $nameHotel = $parentProduct->name;
                    $address = $parentProduct->address;
                }
                $days = $orderProduct->options["days"]??[];
                $checkin = null;
                $checkout = null;
                if (!empty($days)){
                    $checkin = Carbon\Carbon::parse(reset($days))->format("d/m/Y");
                    if (count($days)>1){
                        $checkout = Carbon\Carbon::parse(end($days))->format("d/m/Y");
                    }else{
                        $checkout = Carbon\Carbon::parse(reset($days))->addDay()->format("d/m/Y");
                    }
                }

                $services = $orderProduct->options["services"]??[];
                $filteredServices = array_filter($services, function ($service) {
                    return isset($service["quantity"]) && $service["quantity"]>0;
                });

                $totalPrice = $orderProduct->qty * $orderProduct->price + ($orderProduct->options["addOn"]??0) + ($orderProduct->options["priceChildrenSurplus"]??0) + ($orderProduct->options["price_service_other"]??0);

            @endphp
            <section class="section">
                <h2 class="section-title">THÔNG TIN {{$product->type=="tour"?"TOUR":"HOTEL/RESORT"}}</h2>
                <div class="info-item">
            <span class="label">
              <div class="icon-wrapper">

                <img src="{{ Theme::asset()->url('images/emails/ten.png') }}"
                     >
              </div>
              Tên {{$product->type=="tour"?"Tour: ":"Hotel/Resort: "}}:
            </span>
                    <span class="value">{{$nameHotel}}</span>
                </div>
                <div class="info-item">
            <span class="label">
              <div class="icon-wrapper">

                <img src="{{ Theme::asset()->url('images/emails/address.png') }}"
                     >
              </div>
              Địa Chỉ:
            </span>
                    <span class="value">{{$address}}</span>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">CHI TIẾT ĐƠN HÀNG</h2>
                <div class="tour-item">
                    <img
                        src="{{ RvMedia::getImageUrl($orderProduct->product_image, 'thumb') }}"
                        class="tour-image"
                    />
                    <div class="tour-details">
                        <div class="tour-name">
                            {{ $orderProduct->product_name }}
                        </div>
                        <div>Checkin: {{$checkin}}</div>
                        @if($product->type!="tour")
                            <div>Checkout: {{$checkout}}</div>
                            <div>Số lượng phòng: {{$orderProduct->qty}}</div>
                        @endif
                        <div>Số lượng người: {{$orderProduct->options["adults_number"]??0}} người
                            lớn, {{$orderProduct->options["children_number"]??0}} trẻ em
                        </div>
                        <div class="tour-price">{{ format_price($totalPrice) }}</div>
                    </div>
                </div>
            </section>
            @if($filteredServices)
                <section class="section">
                    <h2 class="section-title">DỊCH VỤ ĐI KÈM</h2>
                    @foreach($filteredServices as $service)
                        <div class="info-item">
                            <span class="label">
                              <div class="icon-wrapper">
                                <img src="{{ Theme::asset()->url('images/emails/ticket.png') }}"
                                     >
                              </div>
                              {{$service["name"]}}(x{{$service["quantity"]}}
                            </span>
                            <span class="value">{{format_price($service["price"])}}</span>
                        </div>
                    @endforeach
                </section>
            @endif
        @endforeach

        <section class="section">
            <h2 class="section-title">THÔNG TIN KHÁCH HÀNG</h2>
            <div class="info-item">
            <span class="label">
              <div class="icon-wrapper">

                <img src="{{ Theme::asset()->url('images/emails/user.png') }}"
                     >
              </div>
              Họ và tên:
            </span>
                <span class="value">{{$order->userName}}</span>
            </div>
            <div class="info-item">
            <span class="label">
              <div class="icon-wrapper">

                <img src="{{ Theme::asset()->url('images/emails/birthday.png') }}"
                     >
              </div>
              Ngày Sinh:
            </span>
                <span class="value">{{\Carbon\Carbon::parse($order->user->dob)->format("d/m/Y")}}</span>
            </div>
            <div class="info-item">
            <span class="label">
              <div class="icon-wrapper">

                <img src="{{ Theme::asset()->url('images/emails/phone.png') }}"
                     >
              </div>
              Số điện thoại:
            </span>
                <span class="value">{{@$order->address->phone}}</span>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">THÔNG TIN THANH TOÁN</h2>
            <div class="info-item">
                <span class="label">Hình thức thanh toán: </span>
                <span class="value">{{strtoupper(@$order->payment->payment_channel)}}</span>
            </div>
        </section>

    </main>
    <footer class="invoice-footer">
        <div class="signature">
            <p class="sincerely">Trân trọng</p>
            <p class="name">{{$order->userName}}!</p>
            <div class="contact-info">
                <div class="company-name" style="margin-right: 50px">
                    <p>Abogo</p>
                </div>
                <div class="contact-details">
                    <p>Mail: {{$companyEmail}}</p>
                    <p>{{$companyName}}</p>
                    <p>{{$companyAddress}}</p>
                    <p>{{$companyPhone}}</p>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <p>
                Trong quá trình trải nghiệm dịch vụ của {{get_ecommerce_setting('store_name')}}, nếu quý khách hàng có
                thắc mắc, yêu cầu, xin vui lòng liên hệ với {{get_ecommerce_setting('store_name')}} qua các kênh liên
                hệ
            </p>
        </div>

        <div class="company-address">
            <p>{{$companyName}}</p>
            <p>Địa chỉ: {{$companyAddress}}</p>
        </div>
    </footer>
</div>
</body>
</html>
