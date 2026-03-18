<?php

namespace Botble\Place\Models;

use Botble\Base\Models\BaseModel;
/**
 * @method static \Botble\Base\Models\BaseQueryBuilder<static> query()
 */
class Placeable extends BaseModel
{
    protected $fillable = [
        'place_id',
        'placeable_id',
        'placeable_type',
    ];

    public function placeable()
    {
        return $this->morphTo();
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
