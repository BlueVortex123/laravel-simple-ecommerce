<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'name' => OrderStatusEnum::class,
    ];

    /**
     * Status has many status lines.
     */
    public function statusLines(): HasMany
    {
        return $this->hasMany(OrderStatusLine::class);
    }
}
