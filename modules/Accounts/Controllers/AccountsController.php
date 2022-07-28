<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Resources\ResourceForTable;
use Modules\Dashboard\Services\Counters;
use Modules\Investments\Models\Deposit;

class AccountsController extends Controller
{
    public function store(Request $request): array
    {
        $fields = $request->validate(
            [
                'name'     => ['required'],
                'balance'  => ['required', 'numeric'],
                'currency' => ['required'],
            ]
        );

        $id = $request->input('id');
        if ($id) {
            $account = Account::findOrFail($id);
            $account->update(
                [
                    'name'     => $fields['name'],
                    'balance'  => $fields['balance'],
                    'currency' => $fields['currency'],
                ]
            );
        } else {
            $account = Account::create(
                [
                    'name'     => $fields['name'],
                    'balance'  => $fields['balance'],
                    'currency' => $fields['currency'],
                    'user_id'  => Auth::user()->id,
                ]
            );
        }
        return ['success' => true, 'id' => $account->id];
    }

    public function edit(int $id): array
    {
        $account = Account::findOrFail($id);
        return [
            'form' => [
                'id'       => $account->id,
                'name'     => $account->name,
                'balance'  => $account->balance,
                'currency' => $account->currency,
            ],
        ];
    }

    public function delete(int $id): array
    {
        $category = Account::findOrFail($id);
        foreach ($category->assets as $asset) {
            $asset->delete();
        }
        $category->delete();
        return ['success' => true];
    }

    public function summary(Counters $counters): array
    {
        $invested = Deposit::sum('sum');
        $allAssetsSum = $counters->getAllAssetsSum();
        $profit = $allAssetsSum - $invested;
        $profitPercent = round($profit / $invested * 100, 2);

        return [
            'summary' => [
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

    public function index(): array
    {
        $accounts = Account::forCurrentUser()->activeAssets()->get();
        return (new ResourceForTable($accounts))->toArray();
    }

    public function updateData(): array
    {
        Artisan::call('securities:update-data');
        return ['success' => true];
    }
}
