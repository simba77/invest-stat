<?php

declare(strict_types=1);

namespace Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;
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

    public function summary(): array
    {
        return [
            'summary' => [
                [
                    'name'  => 'Salary',
                    'total' => config('invest.salary'),
                ],
                [
                    'name'  => 'All Expenses',
                    'total' => 0,
                ],
                [
                    'name'     => 'Salary - Expenses',
                    'helpText' => 'Free Money for Investments',
                    'total'    => config('invest.salary') - 0,
                ],
            ],
        ];
    }

    public function index(): array
    {
        $user = Auth::user();
        $accounts = Account::where('user_id', $user->id)
            ->with(
                [
                    'assets' => function (HasMany $query) {
                        return $query->where('status', '!=', Asset::SOLD)->orWhereNull('status');
                    },
                ]
            )->get();
        return (new ResourceForTable($accounts))->toArray();
    }

    public function updateData(): array
    {
        Artisan::call('securities:update-data');
        return ['success' => true];
    }
}
