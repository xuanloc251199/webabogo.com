<?php

namespace Botble\Extensions\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Product;

/**
 * @method static \Botble\Base\Models\BaseQueryBuilder<static > query()
 */
class Extensions extends BaseModel
{
    protected $table = 'extensions';

    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    protected static function booted(): void
    {
        static::deleted(function (Extensions $extensions) {
            $extensions->products()->detach();
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_extensions', 'extension_id', 'product_id');
    }
}
