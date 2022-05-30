<?php

declare(strict_types=1);

namespace Modules\Accounts\Services;

use Modules\Accounts\Models\Account;
use Modules\Markets\Models\Security;

class AccountService
{
    public function updateAll(): void
    {
        $accounts = Account::query()->get();
        foreach ($accounts as $account) {
            $this->updateSummary($account);
        }
    }

    public function updateSummary(Account $account): void
    {
        $startSum = 0;
        $currentSum = 0;
        foreach ($account->assets as $asset) {
            $stock = Security::query()->where('stock_market', $asset->stock_market)->where('ticker', $asset->ticker)->first();
            $fillBuyPrice = $asset->quantity * $asset->buy_price;
            $fullPrice = $asset->quantity * ($stock?->price ?? 0);
            $startSum += $fillBuyPrice;
            $currentSum += $fullPrice;
        }

        $account->start_sum_of_assets = $startSum;
        $account->current_sum_of_assets = $currentSum;
        $account->save();
    }
}
