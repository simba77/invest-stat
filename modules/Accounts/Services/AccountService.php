<?php

declare(strict_types=1);

namespace Modules\Accounts\Services;

use Modules\Accounts\Models\Account;

class AccountService
{
    public function updateAll(): void
    {
        $accounts = Account::query()->activeAssets()->get();
        foreach ($accounts as $account) {
            $this->updateSummary($account);
        }
    }

    public function updateSummary(Account $account): void
    {
        $startSum = 0;
        $currentSum = 0;
        foreach ($account->assets as $asset) {
            $startSum += $asset->full_buy_price;
            $currentSum += $asset->full_current_base_price;
        }

        $account->start_sum_of_assets = $startSum;
        $account->current_sum_of_assets = $currentSum;
        $account->save();
    }
}
