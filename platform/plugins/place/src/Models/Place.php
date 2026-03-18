<?php

namespace Botble\Place\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Blog\Models\Post;
use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static \Botble\Base\Models\BaseQueryBuilder<static> query()
 */
class Place extends BaseModel
{
    protected $table = 'places';

    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

//    public function posts(): BelongsToMany
//    {
//        return $this->belongsToMany(Post::class, 'post_place', "place_id", "post_id");
//    }

    public function placeables(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Placeable::class);
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Post::class, 'placeable');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Product::class, 'placeable');
    }
}
