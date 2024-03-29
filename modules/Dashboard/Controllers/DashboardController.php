<?php

declare(strict_types=1);

namespace Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Modules\Accounts\Models\Account;
use Modules\Investments\Models\Deposit;
use Modules\Markets\DataProviders\Moex;
use Modules\Savings\Models\Saving;
use Modules\Savings\Services\SavingsService;

class DashboardController extends Controller
{
    public function index(Moex $moex, SavingsService $savings): array
    {
        $invested = Deposit::sum('sum');
        $allAssetsSum = 0;

        $brokers = [];
        $accounts = Account::forCurrentUser()->activeAssets()->orderBy('sort')->get();
        foreach ($accounts as $account) {
            $current_sum_of_assets = $account->current_sum_of_assets + $account->balance + ($account->usd_balance * $moex->getRate());
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
                'name'  => $account->name,
                'cards' => $cards,
            ];
        }

        $profit = $allAssetsSum - $invested;
        $profitPercent = round($profit / $invested * 100, 2);

        $savingAmount = Saving::query()->forCurrentUser()->sum('sum');

        return [
            'usd'            => $moex->getRate(),
            'savingAccounts' => $savings->getGroupedByAccount(),
            'summary'        => [
                [
                    'name'     => 'The Invested Amount',
                    'total'    => $invested,
                    'currency' => '₽',
                ],
                [
                    'name'     => 'The Saving Amount',
                    'total'    => $savingAmount,
                    'currency' => '₽',
                ],
                [
                    'name'     => 'Savings + Invested',
                    'helpText' => 'The Saving Amount + The Invested Amount',
                    'total'    => $invested + $savingAmount,
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
                    'total'    => $allAssetsSum + $savingAmount,
                    'currency' => '₽',
                ],
            ],
            'brokers'        => $brokers,
        ];
    }
}
