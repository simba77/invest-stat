<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthController;
use Modules\Expenses\Controllers\ExpensesController;

Route::group(['prefix' => 'api'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    // For authorized users
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/checkAuth', [AuthController::class, 'getCurrentAuth'])->name('api.currentAuth');

        Route::group(['prefix' => 'expenses'], function () {
            Route::get('list', [ExpensesController::class, 'expensesList'])->name('expenses.expenses-list');
            Route::post('store-category', [ExpensesController::class, 'storeCategory'])->name('expenses.store-category');
            Route::get('edit-category/{id:number}', [ExpensesController::class, 'editCategory'])->name('expenses.edit-category');
            Route::post('delete-category/{id:number}', [ExpensesController::class, 'deleteCategory'])->name('expenses.delete-category');
            Route::post('delete-expense/{id:number}', [ExpensesController::class, 'deleteExpense'])->name('expenses.delete-expense');
            Route::get('edit-expense/{id:number}', [ExpensesController::class, 'editExpense'])->name('expenses.edit-expense');
            Route::post('store-expense/{category:number}', [ExpensesController::class, 'storeExpense'])->name('expenses.store-expense');
        });
    });
});

Route::get('/{any}', fn() => view('spa'))
    ->where('any', '.*')
    ->name('spa');
