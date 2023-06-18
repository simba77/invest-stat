<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Resources\AccountDetail;
use Modules\Accounts\Resources\AccountsCollection;
use Modules\Accounts\Resources\ResourceForTable;
use Modules\Dashboard\Services\Counters;
use Modules\Investments\Models\Deposit;

class AccountsController extends Controller
{
    public function store(Request $request): array
    {
        $fields = $request->validate(
            [
                'name'               => ['required'],
                'balance'            => ['required', 'numeric'],
                'usd_balance'        => ['sometimes', 'numeric'],
                'commission'         => ['sometimes', 'numeric'],
                'futures_commission' => ['sometimes', 'numeric'],
                'sort'               => ['required', 'numeric'],
            ]
        );

        $id = $request->input('id');
        if ($id) {
            $account = Account::findOrFail($id);
            $account->update(
                [
                    'name'               => $fields['name'],
                    'balance'            => $fields['balance'],
                    'usd_balance'        => $fields['usd_balance'],
                    'commission'         => $fields['commission'],
                    'futures_commission' => $fields['futures_commission'],
                    'sort'               => $fields['sort'],
                ]
            );
        } else {
            $account = Account::create(
                [
                    'name'               => $fields['name'],
                    'balance'            => $fields['balance'],
                    'usd_balance'        => $fields['usd_balance'],
                    'commission'         => $fields['commission'],
                    'futures_commission' => $fields['futures_commission'],
                    'sort'               => $fields['sort'],
                    'user_id'            => Auth::user()->id,
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
                'id'                 => $account->id,
                'name'               => $account->name,
                'balance'            => $account->balance,
                'usd_balance'        => $account->usd_balance,
                'commission'         => $account->commission,
                'futures_commission' => $account->futures_commission,
                'sort'               => $account->sort,
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

    /**
     * @deprecated
     */
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

    /**
     * @deprecated
     */
    public function index(): array
    {
        $accounts = Account::forCurrentUser()->withSum('deposits', 'sum')->activeAssets()->get();
        return (new ResourceForTable($accounts))->toArray();
    }

    public function newIndex(): AccountsCollection
    {
        $accounts = Account::forCurrentUser()
            ->withSum('deposits', 'sum')
            ->activeAssets()
            ->orderBy('sort')
            ->get();
        return new AccountsCollection($accounts);
    }

    public function getAccount(int $id): AccountDetail
    {
        $accounts = Account::forCurrentUser()
            ->withSum('deposits', 'sum')
            ->activeAssets()
            ->orderBy('sort')
            ->findOrFail($id);
        return new AccountDetail($accounts);
    }

    /**
     * @deprecated
     */
    public function updateData(): array
    {
        Artisan::call('securities:update-data');
        return ['success' => true];
    }
}
