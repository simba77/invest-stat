<?php

declare(strict_types=1);

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Markets\DataProviders\Moex;
use Modules\Markets\Models\Security;
use Modules\System\Database\CreatedByTrait;

class Asset extends Model
{
    public const SOLD = 1;

    public const TYPE_SHORT = 1;

    use CreatedByTrait;

    protected $fillable = [
        'user_id',
        'currency',
        'buy_price',
        'target_price',
        'sell_price',
        'account_id',
        'quantity',
        'status',
        'stock_market',
        'ticker',
        'type',
        'blocked',
    ];

    protected $casts = [
        'blocked' => 'boolean',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function security(): HasOne
    {
        return $this->hasOne(Security::class, 'ticker', 'ticker');
    }

    public function buyPrice(): Attribute
    {
        return Attribute::get(
            function ($value) {
                if ($this->security->is_future) {
                    return $value * $this->security->step_price * $this->security->lot_size;
                }
                return $value;
            }
        );
    }

    public function targetPrice(): Attribute
    {
        return Attribute::get(
            function ($value) {
                if ($this->security->is_future) {
                    return $value * $this->security->step_price * $this->security->lot_size;
                }
                return $value;
            }
        );
    }

    /**
     * Полная стоимость на момент покупки
     */
    public function fullBuyPrice(): Attribute
    {
        return Attribute::get(
            fn() => ($this->buy_price * $this->quantity)
        );
    }

    /**
     * Полная целевая стоимость
     */
    public function fullTargetPrice(): Attribute
    {
        return Attribute::get(
            fn() => ($this->target_price * $this->quantity)
        );
    }

    /**
     * Текущая стоимость
     */
    public function currentPrice(): Attribute
    {
        return Attribute::get(
            function () {
                if ($this->security->is_future) {
                    return $this->security->price * $this->security->step_price * $this->security->lot_size;
                }
                return (float) $this->security->price;
            }
        );
    }

    /**
     * Текущая полная стоимость
     */
    public function fullCurrentPrice(): Attribute
    {
        return Attribute::get(fn() => ((float) $this->current_price * $this->quantity));
    }

    /**
     * Комиссия за операцию покупки/продажи
     */
    public function commission(): Attribute
    {
        return Attribute::get(function () {
            if ($this->security->is_future) {
                // TODO: Change commission
                return 5 * $this->quantity;
            }
            return $this->full_current_price * ($this->account->commission / 100);
        });
    }

    /**
     * Текущий доход с учетом комиссии
     */
    public function profit(): Attribute
    {
        return Attribute::get(
            function () {
                if ($this->type === Asset::TYPE_SHORT) {
                    return round($this->full_buy_price - $this->full_current_price - $this->commission, 2);
                }
                return round($this->full_current_price - $this->full_buy_price - $this->commission, 2);
            }
        );
    }

    /**
     * Целевой доход
     */
    public function targetProfit(): Attribute
    {
        return Attribute::get(
            function () {
                if (empty($this->target_price)) {
                    return 0;
                }

                if ($this->type === Asset::TYPE_SHORT) {
                    return round($this->buy_price - $this->target_price, 2);
                }
                return round($this->target_price - $this->buy_price, 2);
            }
        );
    }

    public function fullTargetProfit(): Attribute
    {
        return Attribute::get(
            function () {
                if (empty($this->target_price)) {
                    return 0;
                }

                if ($this->type === Asset::TYPE_SHORT) {
                    return round($this->full_buy_price - $this->full_target_price, 2);
                }
                return round($this->full_target_price - $this->full_buy_price, 2);
            }
        );
    }

    public function fullTargetProfitPercent(): Attribute
    {
        return Attribute::get(
            function () {
                if (empty($this->target_price)) {
                    return 0;
                }

                if ($this->type === Asset::TYPE_SHORT) {
                    return round(($this->full_buy_price - $this->full_target_price) / $this->full_buy_price * 100, 2);
                }
                return round(($this->full_target_price - $this->full_buy_price) / $this->full_buy_price * 100, 2);
            }
        );
    }

    /**
     * Текущая полная цена в базовой валюте
     */
    public function fullCurrentBasePrice(): Attribute
    {
        return Attribute::get(
            function () {
                if ($this->currency === 'USD') {
                    $rate = app(Moex::class)->getRate();
                    return round($this->full_current_price * $rate);
                } elseif ($this->currency === 'RUB') {
                    return $this->full_current_price;
                }
                return $this->full_current_price;
            }
        );
    }
}
