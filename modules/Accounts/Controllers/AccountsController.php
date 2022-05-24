<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Expense;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Resources\ResourceForTable;

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
        foreach ($category->expenses as $expense) {
            $expense->delete();
        }
        $category->delete();
        return ['success' => true];
    }

    public function editExpense(int $id): array
    {
        $expense = Expense::findOrFail($id);
        return [
            'form' => [
                'id'   => $expense->id,
                'name' => $expense->name,
                'sum'  => $expense->sum,
            ],
        ];
    }

    public function storeExpense(int $category, Request $request): array
    {
        $fields = $request->validate(
            [
                'name' => ['required'],
                'sum'  => ['required', 'numeric'],
            ]
        );

        $id = $request->input('id');
        if ($id) {
            $expense = Expense::findOrFail($id);
            $expense->update(
                [
                    'name' => $fields['name'],
                    'sum'  => $fields['sum'],
                ]
            );
        } else {
            $expense = Expense::create(
                [
                    'name'        => $fields['name'],
                    'sum'         => $fields['sum'],
                    'category_id' => $category,
                    'user_id'     => Auth::user()->id,
                ]
            );
        }

        return ['success' => true, 'id' => $expense->id];
    }

    public function deleteExpense(int $id): array
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return ['success' => true];
    }

    public function summary(): array
    {
        $expenses = Expense::where('user_id', Auth::user()->id)->sum('sum');
        return [
            'summary' => [
                [
                    'name'  => 'Salary',
                    'total' => config('invest.salary'),
                ],
                [
                    'name'  => 'All Expenses',
                    'total' => $expenses,
                ],
                [
                    'name'     => 'Salary - Expenses',
                    'helpText' => 'Free Money for Investments',
                    'total'    => config('invest.salary') - $expenses,
                ],
            ],
        ];
    }

    public function index(): array
    {
        $user = Auth::user();
        $accounts = Account::where('user_id', $user->id)->get();
        return (new ResourceForTable($accounts))->toArray();
    }
}
