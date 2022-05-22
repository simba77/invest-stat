<?php

declare(strict_types=1);

namespace Modules\Expenses\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Expenses\Models\Expense;
use Modules\Expenses\Models\ExpensesCategory;
use Modules\Expenses\Resources\ResourceForTable;

class ExpensesController extends Controller
{
    public function storeCategory(Request $request): array
    {
        $fields = $request->validate(
            [
                'name' => ['required'],
            ]
        );

        $id = $request->input('id');
        if ($id) {
            $category = ExpensesCategory::findOrFail($id);
            $category->update(
                [
                    'name' => $fields['name'],
                ]
            );
        } else {
            $category = ExpensesCategory::create(
                [
                    'name'    => $fields['name'],
                    'user_id' => Auth::user()->id,
                ]
            );
        }
        return ['success' => true, 'id' => $category->id];
    }

    public function editCategory(int $id): array
    {
        $category = ExpensesCategory::findOrFail($id);
        return [
            'form' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
        ];
    }

    public function deleteCategory(int $id): array
    {
        $category = ExpensesCategory::findOrFail($id);
        foreach ($category->expenses as $expense) {
            $expense->delete();
        }
        $category->delete();
        return ['success' => true];
    }

    public function addExpense(int $category, Request $request): array
    {
        $fields = $request->validate(
            [
                'name' => ['required'],
                'sum'  => ['required', 'numeric'],
            ]
        );
        $expense = Expense::create(
            [
                'name'        => $fields['name'],
                'sum'         => $fields['sum'],
                'category_id' => $category,
                'user_id'     => Auth::user()->id,
            ]
        );
        return ['success' => true, 'id' => $expense->id];
    }

    public function deleteExpense(int $id): array
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return ['success' => true];
    }

    public function expensesList(): array
    {
        $user = Auth::user();
        $expenses = ExpensesCategory::where('user_id', $user->id)->get();
        return (new ResourceForTable($expenses))->toArray();
    }
}
