<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Accounts\Models\Asset;

/**
 * @mixin Asset
 */
class AssetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
            'accountId'   => $this->account_id,
            'ticker'      => $this->ticker,
            'name'        => $this->security->short_name,
            'stockMarket' => $this->stock_market,
            'blocked'     => $this->blocked,
            'quantity'    => $this->quantity,

            'avgBuyPrice'  => $this->buy_price,
            'fullBuyPrice' => $this->full_buy_price,

            'avgTargetPrice'  => $this->target_price,
            'fullTargetPrice' => $this->full_target_price,

            'currentPrice'     => $this->current_price,
            'fullCurrentPrice' => $this->full_current_price,

            'profit'         => $this->profit,
            'profitPercent'  => round($this->profit / $this->full_buy_price * 100, 2),
            'commission'     => $this->commission,
            'fullCommission' => $this->commission,

            'targetProfit'            => $this->target_profit,
            'fullTargetProfit'        => $this->full_target_profit,
            'fullTargetProfitPercent' => $this->full_target_profit_percent,

            'groupPercent' => round($this->full_current_base_price / ($this->account->current_sum_of_assets + $this->account->balance) * 100, 2),

            'currency' => $this->currency,
            'type'     => $this->type,
            'isShort'  => $this->type === Asset::TYPE_SHORT,
        ];
    }
}
