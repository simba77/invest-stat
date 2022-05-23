<?php

declare(strict_types=1);

namespace Modules\Investments\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Investments\Models\Deposit;
use Modules\Investments\Resources\DepositResource;

class DepositsController extends Controller
{
    public function store(Request $request): array
    {
        $fields = $request->validate(
            [
                'date' => ['required'],
                'sum'  => ['required', 'numeric'],
            ]
        );

        $deposit = Deposit::create(
            [
                'user_id' => Auth::user()->id,
                'date'    => $fields['date'],
                'sum'     => $fields['sum'],
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
        $deposits = Deposit::where('user_id', $user->id)->orderBy('date')->get();
        return DepositResource::collection($deposits);
    }
}
