<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;

class AssetsController extends Controller
{
    public function edit(int $id): array
    {
        $asset = Asset::findOrFail($id);
        return [
            'form' => [
                'id'     => $asset->id,
                'ticker' => $asset->ticker,
                'sum'    => $asset->sum,
            ],
        ];
    }

    public function store(int $account, Request $request): array
    {
        $fields = $request->validate(
            [
                'ticker'       => ['required'],
                'stock_market' => ['required'],
                'quantity'     => ['required', 'numeric'],
                'buy_price'    => ['required', 'numeric'],
                'currency'     => ['required'],
            ]
        );

        $id = $request->input('id');
        if ($id) {
            $asset = Asset::findOrFail($id);
            $asset->update(
                [
                    'ticker'       => $fields['ticker'],
                    'stock_market' => $fields['stock_market'],
                    'quantity'     => $fields['quantity'],
                    'buy_price'    => $fields['buy_price'],
                    'currency'     => $fields['currency'],
                ]
            );
        } else {
            $asset = Asset::create(
                [
                    'ticker'       => $fields['ticker'],
                    'stock_market' => $fields['stock_market'],
                    'quantity'     => $fields['quantity'],
                    'buy_price'    => $fields['buy_price'],
                    'currency'     => $fields['currency'],
                    'account_id'   => $account,
                    'user_id'      => Auth::user()->id,
                ]
            );

            // Change the balance
            $account = Account::findOrFail($asset->account_id);
            $sum = $fields['buy_price'] * $fields['quantity'];
            $account->balance -= $sum;
            $account->save();
        }

        return ['success' => true, 'id' => $asset->id];
    }

    public function delete(int $id): array
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return ['success' => true];
    }

    public function sell(int $id, Request $request): array
    {
        $fields = $request->validate(
            [
                'price' => ['required', 'numeric'],
            ]
        );

        DB::transaction(function () use ($id, $fields) {
            $asset = Asset::findOrFail($id);
            $asset->update(['sell_price' => $fields['price'], 'status' => Asset::SOLD]);

            // Change the balance
            $account = Account::findOrFail($asset->account_id);
            $sum = $fields['price'] * $asset->quantity;
            $account->balance += $sum;
            $account->save();
        });
        return ['success' => true];
    }
}
