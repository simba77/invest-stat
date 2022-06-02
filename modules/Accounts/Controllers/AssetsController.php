<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Asset;

class AssetsController extends Controller
{
    public function edit(int $id): array
    {
        $expense = Asset::findOrFail($id);
        return [
            'form' => [
                'id'   => $expense->id,
                'name' => $expense->name,
                'sum'  => $expense->sum,
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
            $expense = Asset::findOrFail($id);
            $expense->update(
                [
                    'ticker'       => $fields['ticker'],
                    'stock_market' => $fields['stock_market'],
                    'quantity'     => $fields['quantity'],
                    'buy_price'    => $fields['buy_price'],
                    'currency'     => $fields['currency'],
                ]
            );
        } else {
            $expense = Asset::create(
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
        }

        return ['success' => true, 'id' => $expense->id];
    }

    public function delete(int $id): array
    {
        $expense = Asset::findOrFail($id);
        $expense->delete();
        return ['success' => true];
    }

    public function sell(int $id, Request $request): array
    {
        $fields = $request->validate(
            [
                'price' => ['required', 'numeric'],
            ]
        );
        $expense = Asset::findOrFail($id);
        $expense->update(['sell_price' => $fields['price'], 'status' => Asset::SOLD]);
        return ['success' => true];
    }
}
