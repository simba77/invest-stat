<?php

declare(strict_types=1);

namespace Modules\Markets\Models;

use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    protected $fillable = [
        'ticker',
        'name',
        'short_name',
        'lat_name',
        'stock_market',
        'currency',
        'price',
        'lot_size',
        'isin',
        'is_future',
        'expiration',
        'step_price',
    ];

    protected $casts = [
        'expiration' => 'date',
        'is_future'  => 'boolean',
    ];
}
