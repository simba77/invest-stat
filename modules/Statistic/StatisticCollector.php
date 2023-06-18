<?php

declare(strict_types=1);

namespace Modules\Statistic;

use Carbon\Carbon;
use Modules\Accounts\Models\Account;
use Modules\Markets\DataProviders\Moex;
use Modules\Statistic\Models\Statistic;

class StatisticCollector
{
    public function collectStatisticForAccounts()
    {
        $accounts = Account::withSum('deposits', 'sum')
            ->activeAssets()
            ->orderBy('sort')
            ->get();

        $moex = app(Moex::class);
        $accounts->map(function (Account $account) use ($moex) {
            $currentValue = round($account->current_sum_of_assets + $account->balance + ($account->usd_balance * $moex->getRate()), 2);
            Statistic::query()->create(
                [
                    'account_id'  => $account->id,
                    'date'        => Carbon::now(),
                    'balance'     => $account->balance,
                    'usd_balance' => $account->usd_balance,
                    'deposits'    => $account->deposits_sum_sum ?? 0,
                    'current'     => $currentValue,
                    'profit'      => round($currentValue - (float) $account->deposits_sum_sum, 2),
                ]
            );
        });
    }
}
