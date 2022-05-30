<?php

declare(strict_types=1);

namespace Modules\Dashboard\Services;

use Modules\Accounts\Models\Account;
use Modules\Markets\DataProviders\Moex;

class Counters
{
    public function __construct(private Moex $moex)
    {
    }

    public function getAllAssetsSum(): float
    {
        $sum = 0;
        $accounts = Account::forCurrentUser()->get();
        foreach ($accounts as $account) {
            if ($account->currency === 'USD') {
                $sum += ($account->current_sum_of_assets + $account->balance) * $this->moex->getRate();
            } else {
                $sum += $account->current_sum_of_assets + $account->balance;
            }
        }

        return $sum;
    }
}
