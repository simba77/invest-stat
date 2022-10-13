<?php

declare(strict_types=1);

namespace Modules\Investments\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Account;
use Modules\Investments\Models\Deposit;
use Modules\Investments\Resources\DepositResource;

class DepositsController extends Controller
{
    public function store(Request $request): array
    {
        $fields = $request->validate(
            [
                'date'    => ['required'],
                'sum'     => ['required', 'numeric'],
                'account' => ['numeric'],
            ]
        );

        $deposit = Deposit::create(
            [
                'user_id'    => Auth::user()->id,
                'date'       => $fields['date'],
                'sum'        => $fields['sum'],
                'account_id' => $fields['account'],
            ]
        );

        return ['success' => true, 'id' => $deposit->id];
    }

    public function delete(int $id): array
    {
        $expense = Deposit::findOrFail($id);
        $expense->delete();
        return ['success' => true];
    }

    public function depositsList(): AnonymousResourceCollection
    {
        $user = Auth::user();
        $deposits = Deposit::where('user_id', $user->id)->orderByDesc('date')->get();
        return DepositResource::collection($deposits);
    }

    public function edit(int $id): array
    {
        $deposit = Deposit::find($id);

        $accounts = Account::query()->get();

        if ($deposit) {
            return [
                'form'     => [
                    'id'      => $deposit->id,
                    'date'    => $deposit->date->format('d.m.Y'),
                    'account' => $deposit->account_id,
                    'sum'     => $deposit->sum,
                ],
                'accounts' => $accounts,
            ];
        }

        return [
            'form'     => [
                'date'    => null,
                'account' => null,
                'sum'     => null,
            ],
            'accounts' => $accounts,
        ];
    }
}
