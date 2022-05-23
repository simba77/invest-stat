<?php

declare(strict_types=1);

namespace Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Modules\Investments\Models\Deposit;

class DashboardController extends Controller
{
    public function index(): array
    {
        $invested = Deposit::sum('sum');
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
                    'total'    => $invested + config('invest.savingAmount'),
                    'currency' => '₽',
                ],

                [
                    'name'     => 'Profit',
                    'helpText' => 'Assets for The Current Day - The Invested Amount',
                    'percent'  => -27.86,
                    'total'    => -130516.22,
                    'currency' => '₽',
                ],

                [
                    'name'     => 'Saving + All Brokers Assets',
                    'helpText' => 'Assets for The Current Day + The Saving Amount',
                    'total'    => 387893.95,
                    'currency' => '₽',
                ],
            ],
        ];
    }
}
