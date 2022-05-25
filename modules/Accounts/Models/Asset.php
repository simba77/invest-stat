<?php

declare(strict_types=1);

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\System\Database\CreatedByTrait;

class Asset extends Model
{
    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'currency',
        'buy_price',
        'sell_price',
        'account_id',
        'quantity',
        'status',
        'stock_market',
        'ticker',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
