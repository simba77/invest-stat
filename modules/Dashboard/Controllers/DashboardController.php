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
                    'name'     => 'Profit',
                    'helpText' => 'Assets for The Current Day - The Invested Amount',
                    'percent'  => 132,
                    'total'    => 9334,
                ],
                [
                    'name'  => 'The Invested Amount',
                    'total' => $invested,
                ],
                [
                    'name'  => 'The Saving Amount',
                    'total' => config('invest.savingAmount'),
                ],
                [
                    'name'     => 'Savings + Invested',
                    'helpText' => 'The Saving Amount + The Invested Amount',
                    'total'    => $invested + config('invest.savingAmount'),
                ],
                [
                    'name'     => 'Savings and Investments (Today)',
                    'helpText' => 'Saving + Assets for The Current Day',
                    'total'    => $invested + config('invest.savingAmount'),
                ],
            ],
        ];
    }
}
