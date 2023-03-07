<?php

declare(strict_types=1);

namespace Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounts\Models\Account;
use Modules\Investments\Models\Deposit;
use Modules\Markets\DataProviders\Moex;

class DashboardController extends Controller
{
    public function index(Moex $moex): array
    {
        $invested = Deposit::sum('sum');
        $allAssetsSum = 0;

        $brokers = [];
        $accounts = Account::forCurrentUser()->activeAssets()->get();
        foreach ($accounts as $account) {
            $current_sum_of_assets = $account->assets->sum('full_current_base_price');
            $allAssetsSum += $current_sum_of_assets;

            $cards = [
                [
                    'name'    => 'Profit',
                    'help'    => 'Current Value + Balance - Initial Cost',
                    'percent' => $account->profit_percent,
                    'total'   => $account->profit,
                ],
                [
                    'name'  => 'Current Value',
                    'help'  => null,
                    'total' => $current_sum_of_assets,
                ],
                [
                    'name'  => 'Initial Cost',
                    'help'  => null,
                    'total' => $account->start_sum_of_assets,
                ],
            ];

            $brokers[] = [
                'name'     => $account->name,
                'currency' => getCurrencyName($account->currency),
                'cards'    => $cards,
            ];
        }

        $profit = $allAssetsSum - $invested;
        $profitPercent = round($profit / $invested * 100, 2);

        return [
            'usd'     => $moex->getRate(),
            'summary' => [
                [
                    'name'     => 'The Invested Amount',
                    'total'    => $invested,
                    'currency' => '₽',
                ],
                [
                    'name'     => 'The Saving Amount',
                    'total'    => config('invest.savingAmount'),
                    'currency' => '₽',
                ],
                [
                    'name'     => 'Savings + Invested',
                    'helpText' => 'The Saving Amount + The Invested Amount',
                    'total'    => $invested + config('invest.savingAmount'),
                    'currency' => '₽',
                ],

                [
                    'name'     => 'All Assets',
                    'helpText' => 'The sum of all assets held by brokers',
                    'total'    => $allAssetsSum,
                    'currency' => '₽',
                ],

                [
                    'name'     => 'Profit',
                    'helpText' => 'Assets for The Current Day - The Invested Amount',
                    'percent'  => $profitPercent,
                    'total'    => $profit,
                    'currency' => '₽',
                ],

                [
                    'name'     => 'Saving + All Brokers Assets',
                    'helpText' => 'Assets for The Current Day + The Saving Amount',
                    'total'    => $allAssetsSum + config('invest.savingAmount'),
                    'currency' => '₽',
                ],
            ],
            'brokers' => $brokers,
        ];
    }
}
