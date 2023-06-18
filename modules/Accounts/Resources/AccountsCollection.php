<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;
use Modules\Accounts\Services\GroupAssetsCalculator;
use Modules\Markets\DataProviders\Moex;

/** @see \Modules\Accounts\Models\Account */
class AccountsCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): Collection
    {
        $moex = app(Moex::class);
        return $this->collection->map(function (Account $account) use ($moex) {
            $currentValue = round($account->current_sum_of_assets + $account->balance + ($account->usd_balance * $moex->getRate()), 2);
            return [
                'id'           => $account->id,
                'name'         => $account->name,
                'balance'      => $account->balance,
                'usdBalance'   => $account->usd_balance,
                'deposits'     => $account->deposits_sum_sum ?? 0,
                'currentValue' => $currentValue,
                'fullProfit'   => round($currentValue - (float) $account->deposits_sum_sum, 2),
            ];
        });
    }
}
