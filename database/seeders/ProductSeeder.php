<?php

namespace Database\Seeders;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductFile;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Models\ShipmentHistory;
use Botble\Ecommerce\Models\Wishlist;
use Botble\Ecommerce\Services\Products\StoreProductService;
use Botble\Payment\Models\Payment;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('products');

        $faker = fake();

        $products = [
            [
                'name' => 'Furama resort Danang - Thiên Đường Nghỉ Dưỡng Ven Biển',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Villa Biển Hồ Tràm Sang Trọng - Có Hồ Bơi Riêng',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Resort 5 Sao Đà Nẵng View Biển – Miễn Phí Buffet Sáng',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Khách Sạn Trung Tâm Hà Nội – Gần Hồ Hoàn Kiếm',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Căn Hộ Apartment Quận 1 – Gần Phố Tây Bùi Viện',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Gói Spa Cao Cấp – Thư Giãn Toàn Thân Tại TP.HCM',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Du Thuyền 5 Sao Hạ Long – 2 Ngày 1 Đêm',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'TOUR 5N4Đ: HÀ NỘI – BANGKOK - PATTAYA – HÀ NỘI BAY VJ/VU',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Combo Du Lịch Phú Quốc – Vé Máy Bay + Khách Sạn 3N2Đ',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Gói Check-in Chụp Hình Cầu Vàng Bà Nà Hills',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Dịch Vụ Checkout Trễ Tại Khách Sạn Đà Lạt',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Dịch Vụ Đưa Đón Sân Bay Nội Bài – Xe Riêng 4 Chỗ',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Thuê Xe 7 Chỗ Có Tài Xế – TP.HCM – Vũng Tàu',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Hộp Quà Tết Cao Cấp – Đặc Sản Miền Trung',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Bài Review Du Lịch Đà Lạt 3N2Đ Tự Túc',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Vé Tham Quan VinWonders Phú Quốc',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
            [
                'name' => 'Gói Chơi Golf 18 Lỗ Tại Sân Golf Tân Sơn Nhất',
                'price' => $faker->numberBetween(5100000, 10000000),
                'sale_price' => $faker->numberBetween(2500000, 5000000),
                'is_featured' => $faker->boolean,
            ],
        ];

        Product::query()->truncate();
        DB::table('ec_product_with_attribute_set')->truncate();
        DB::table('ec_product_variations')->truncate();
        DB::table('ec_product_variation_items')->truncate();
        DB::table('ec_product_collection_products')->truncate();
        DB::table('ec_product_label_products')->truncate();
        DB::table('ec_product_category_product')->truncate();
        DB::table('ec_product_related_relations')->truncate();
        Slug::query()->where('reference_type', Product::class)->delete();
        Wishlist::query()->truncate();
        Order::query()->truncate();
        OrderAddress::query()->truncate();
        OrderProduct::query()->truncate();
        OrderHistory::query()->truncate();
        Shipment::query()->truncate();
        ShipmentHistory::query()->truncate();
        Payment::query()->truncate();

        MetaBoxModel::query()->where('reference_type', Product::class)->delete();
        ProductFile::query()->truncate();

        foreach ($products as $key => $item) {
            if (!isset($item['description'])) {
                $item['description'] = $faker->sentence(10);
            }

            $item['content'] = $faker->paragraphs(3, true);
            $item['status'] = BaseStatusEnum::PUBLISHED;
            $item['sku'] = 'HS-' . $faker->numberBetween(100, 200);
            $item['brand_id'] = 1;
            $item['views'] = $faker->numberBetween(1000, 200000);
            $item['quantity'] = $faker->numberBetween(10, 20);
            $item['length'] = $faker->numberBetween(10, 20);
            $item['wide'] = $faker->numberBetween(10, 20);
            $item['height'] = $faker->numberBetween(10, 20);
            $item['weight'] = $faker->numberBetween(500, 900);
            $item['with_storehouse_management'] = true;

            // Support Digital Product
            $productName = $item['name'];

            $images = [
                'products/' . ($key + 1) . '.jpg',
            ];

            for ($i = 1; $i <= 10; $i++) {
                if (File::exists(database_path('seeders/files/products/' . ($key + 1) . '-' . $i . '.jpg'))) {
                    $images[] = 'products/' . ($key + 1) . '-' . $i . '.jpg';
                }
            }

            $item['images'] = json_encode($images);

            $product = Product::query()->create(Arr::except($item, ['layout']));

            $layout = $item['layout'] ?? null;
            if ($layout) {
                MetaBox::saveMetaBoxData($product, 'layout', $layout);
            }

            $product->productCollections()->sync([$faker->numberBetween(1, 3)]);

            if ($product->id % 7 == 0) {
                $product->productLabels()->sync([$faker->numberBetween(1, 3)]);
            }

            $product->categories()->sync([
                $faker->numberBetween(1, 16),
            ]);

            $product->tags()->sync([
                $faker->numberBetween(1, 6),
                $faker->numberBetween(1, 6),
                $faker->numberBetween(1, 6),
            ]);

            $product->taxes()->sync([1]);

            SlugHelper::createSlug($product);

            MetaBox::saveMetaBoxData(
                $product,
                'faq_schema_config',
                json_decode(
                    '[[{"key":"question","value":"What Shipping Methods Are Available?"},{"key":"answer","value":"Ex Portland Pitchfork irure mustache. Eutra fap before they sold out literally. Aliquip ugh bicycle rights actually mlkshk, seitan squid craft beer tempor."}],[{"key":"question","value":"Do You Ship Internationally?"},{"key":"answer","value":"Hoodie tote bag mixtape tofu. Typewriter jean shorts wolf quinoa, messenger bag organic freegan cray."}],[{"key":"question","value":"How Long Will It Take To Get My Package?"},{"key":"answer","value":"Swag slow-carb quinoa VHS typewriter pork belly brunch, paleo single-origin coffee Wes Anderson. Flexitarian Pitchfork forage, literally paleo fap pour-over. Wes Anderson Pinterest YOLO fanny pack meggings, deep v XOXO chambray sustainable slow-carb raw denim church-key fap chillwave Etsy. +1 typewriter kitsch, American Apparel tofu Banksy Vice."}],[{"key":"question","value":"What Payment Methods Are Accepted?"},{"key":"answer","value":"Fashion axe DIY jean shorts, swag kale chips meh polaroid kogi butcher Wes Anderson chambray next level semiotics gentrify yr. Voluptate photo booth fugiat Vice. Austin sed Williamsburg, ea labore raw denim voluptate cred proident mixtape excepteur mustache. Twee chia photo booth readymade food truck, hoodie roof party swag keytar PBR DIY."}],[{"key":"question","value":"Is Buying On-Line Safe?"},{"key":"answer","value":"Art party authentic freegan semiotics jean shorts chia cred. Neutra Austin roof party Brooklyn, synth Thundercats swag 8-bit photo booth. Plaid letterpress leggings craft beer meh ethical Pinterest."}]]',
                    true
                )
            );
        }

        $countProducts = count($products);

        $dateAttrIds = ProductAttribute::query()->where('attribute_set_id', 1)->pluck('id')->toArray();
        $ageAttrIds = ProductAttribute::query()->where('attribute_set_id', 2)->pluck('id')->toArray();
        $baseSku = $product->sku;
        foreach ($products as $key => $item) {
            $product = Product::query()->find($key + 1);

            if (!$product) {
                continue;
            }

            $product->productAttributeSets()->sync([1, 2]);

            foreach ($dateAttrIds as $dateAttrId) {
                $randDate = rand(0, 1);

                if ($randDate) {
                    $first = true;
                    foreach ($ageAttrIds as $ageAttrId) {
                        $isDefault = (bool)$first;
                        $first = false;
                        $sku = $baseSku . '-D' . $dateAttrId . '-A' . $ageAttrId;

                        $variation = Product::query()->create([
                            'name' => $product->name,
                            'status' => BaseStatusEnum::PUBLISHED,
                            'sku' => $sku,
                            'quantity' => $product->quantity,
                            'weight' => $product->weight,
                            'height' => $product->height,
                            'wide' => $product->wide,
                            'length' => $product->length,
                            'price' => $product->price,
                            'sale_price' => $product->id % 4 == 0 ? ($product->price - $product->price * $faker->numberBetween(
                                    5,
                                    30
                                ) / 100) : null,
                            'brand_id' => $product->brand_id,
                            'with_storehouse_management' => $product->with_storehouse_management,
                            'images' => json_encode([Arr::first($product->images)]),
                            'is_variation' => true,
                            'product_type' => $product->product_type,
                        ]);

                        $productVariation = ProductVariation::query()->create([
                            'product_id' => $variation->id,
                            'configurable_product_id' => $product->id,
                            'is_default' => $isDefault,
                        ]);

                        if ($productVariation->is_default) {
                            $product->update([
                                'sku' => $variation->sku,
                                'sale_price' => $variation->sale_price,
                            ]);
                        }

                        ProductVariationItem::query()->create([
                            'attribute_id' => $ageAttrId,
                            'variation_id' => $productVariation->id,
                        ]);

                        ProductVariationItem::query()->create([
                            'attribute_id' => $dateAttrId,
                            'variation_id' => $productVariation->id,
                        ]);
                    }
                }
            }
        }
    }
}
