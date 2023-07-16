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
        'is_bond',
        'coupon_percent',
        'coupon_value',
        'coupon_accumulated',
        'next_coupon_date',
        'maturity_date',
    ];

    protected $casts = [
        'expiration'       => 'date',
        'next_coupon_date' => 'date',
        'maturity_date'    => 'date',
        'is_future'        => 'boolean',
        'is_bond'          => 'boolean',
    ];
}
