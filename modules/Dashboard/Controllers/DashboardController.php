<?php

declare(strict_types=1);

namespace Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Modules\Dashboard\Services\Counters;
use Modules\Investments\Models\Deposit;

class DashboardController extends Controller
{
    public function index(Counters $counters): array
    {
        $invested = Deposit::sum('sum');
        $allAssetsSum = $counters->getAllAssetsSum();
        $profit = $allAssetsSum - $invested;
        $profitPercent = round($profit / $invested * 100, 2);

        return [
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
        ];
    }
}
