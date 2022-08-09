<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;
use Modules\Accounts\Resources\SoldAssetsResource;
use Modules\Accounts\Services\AccountService;

class AssetsController extends Controller
{
    public function edit(int $id): array
    {
        $asset = Asset::findOrFail($id);
        return [
            'form' => [
                'id'           => $asset->id,
                'ticker'       => $asset->ticker,
                'stock_market' => $asset->stock_market,
                'quantity'     => $asset->quantity,
                'buy_price'    => $asset->buy_price,
                'target_price' => $asset->target_price,
                'currency'     => $asset->currency,
                'short'        => $asset->type === Asset::TYPE_SHORT,
            ],
        ];
    }

    public function store(int $account, Request $request, AccountService $accountService): array
    {
        $fields = $request->validate(
            [
                'ticker'       => ['required'],
                'stock_market' => ['required'],
                'quantity'     => ['required', 'numeric'],
                'buy_price'    => ['required', 'numeric'],
                'target_price' => ['sometimes', 'numeric'],
                'currency'     => ['required'],
                'short'        => [],
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
                    'target_price' => $fields['target_price'],
                    'currency'     => $fields['currency'],
                    'type'         => ! empty($fields['short']) ? Asset::TYPE_SHORT : null,
                ]
            );
        } else {
            $asset = Asset::create(
                [
                    'ticker'       => $fields['ticker'],
                    'stock_market' => $fields['stock_market'],
                    'quantity'     => $fields['quantity'],
                    'buy_price'    => $fields['buy_price'],
                    'target_price' => $fields['target_price'],
                    'currency'     => $fields['currency'],
                    'account_id'   => $account,
                    'user_id'      => Auth::user()->id,
                    'type'         => ! empty($fields['short']) ? Asset::TYPE_SHORT : null,
                ]
            );

            // Change the balance
            $account = Account::findOrFail($asset->account_id);
            $sum = $fields['buy_price'] * $fields['quantity'];
            if (empty($fields['short'])) {
                $account->balance -= $sum;
            } else {
                $account->balance += $sum;
            }
            $account->save();
        }

        $accountService->updateAll();

        return ['success' => true, 'id' => $asset->id];
    }

    public function delete(int $id, AccountService $accountService): array
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        $accountService->updateAll();
        return ['success' => true];
    }

    public function sell(int $id, Request $request, AccountService $accountService): array
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
            if ($asset->type === Asset::TYPE_SHORT) {
                $account->balance -= $sum;
            } else {
                $account->balance += $sum;
            }
            $account->save();
        });

        $accountService->updateAll();

        return ['success' => true];
    }

    public function soldAssets(): array
    {
        $accounts = Account::forCurrentUser()->soldAssets()->get();
        return (new SoldAssetsResource($accounts))->toArray();
    }
}
