<?php

declare(strict_types=1);

namespace Modules\Expenses\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Expenses\Models\ExpensesCategory;
use Modules\Expenses\Resources\CategoryResource;

class ExpensesController extends Controller
{
    public function createCategory(Request $request): array
    {
        $fields = $request->validate(
            [
                'name' => ['required'],
            ]
        );
        $category = ExpensesCategory::create(
            [
                'name'    => $fields['name'],
                'user_id' => Auth::user()->id,
            ]
        );
        return ['success' => true, 'id' => $category->id];
    }

    public function expensesList(): AnonymousResourceCollection
    {
        $user = Auth::user();
        $expenses = ExpensesCategory::where('user_id', $user->id)->get();
        return CategoryResource::collection($expenses);
    }
}