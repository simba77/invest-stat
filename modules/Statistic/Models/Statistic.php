<?php

declare(strict_types=1);

namespace Modules\Statistic\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $table = 'statistic';

    protected $fillable = [
        'account_id',
        'date',
        'balance',
        'usd_balance',
        'deposits',
        'current',
        'profit',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
