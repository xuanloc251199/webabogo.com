<?php

namespace Botble\Services\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Product;

/**
 * @method static \Botble\Base\Models\BaseQueryBuilder<static> query()
 */
class Services extends BaseModel
{
    protected $table = 'services';

    protected $fillable = [
        'image',
        'name',
        'price',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];
    public function products(){
        return $this->belongsToMany(Product::class, "product_service", "service_id","product_id");
    }
}
