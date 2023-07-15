<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Accounts\Controllers\AccountsController;
use Modules\Accounts\Controllers\AssetsController;
use Modules\Auth\Controllers\AuthController;
use Modules\Dashboard\Controllers\DashboardController;
use Modules\Expenses\Controllers\ExpensesController;
use Modules\Investments\Controllers\DepositsController;
use Modules\Savings\Controllers\SavingAccountsController;
use Modules\Savings\Controllers\SavingsController;

Route::group(['prefix' => 'api'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    // For authorized users
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/checkAuth', [AuthController::class, 'getCurrentAuth'])->name('api.currentAuth');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::group(['prefix' => 'expenses'], function () {
            Route::get('list', [ExpensesController::class, 'expensesList'])->name('expenses.expenses-list');
            Route::get('summary', [ExpensesController::class, 'expensesSummary'])->name('expenses.expenses-summary');
            Route::post('store-category', [ExpensesController::class, 'storeCategory'])->name('expenses.store-category');
            Route::get('edit-category/{id:number}', [ExpensesController::class, 'editCategory'])->name('expenses.edit-category');
            Route::post('delete-category/{id:number}', [ExpensesController::class, 'deleteCategory'])->name('expenses.delete-category');
            Route::post('delete-expense/{id:number}', [ExpensesController::class, 'deleteExpense'])->name('expenses.delete-expense');
            Route::get('edit-expense/{id:number}', [ExpensesController::class, 'editExpense'])->name('expenses.edit-expense');
            Route::post('store-expense/{category:number}', [ExpensesController::class, 'storeExpense'])->name('expenses.store-expense');
        });

        Route::group(['prefix' => 'investments'], function () {
            Route::get('deposits', [DepositsController::class, 'depositsList'])->name('investments.deposits');
            Route::post('deposits/store', [DepositsController::class, 'store'])->name('investments.deposits.store');
            Route::post('deposits/delete/{id:number}', [DepositsController::class, 'delete'])->name('investments.deposits.delete');
            Route::get('deposits/edit/{id:number}', [DepositsController::class, 'edit'])->name('investments.deposits.edit');
        });

        Route::group(['prefix' => 'accounts'], function () {
            Route::get('list', [AccountsController::class, 'index'])->name('accounts.list');
            Route::get('new-list', [AccountsController::class, 'newIndex'])->name('accounts.newList');
            Route::get('summary', [AccountsController::class, 'summary'])->name('accounts.summary');
            Route::post('store', [AccountsController::class, 'store'])->name('accounts.store');
            Route::get('edit/{id:number}', [AccountsController::class, 'edit'])->name('accounts.edit');
            Route::post('delete/{id:number}', [AccountsController::class, 'delete'])->name('accounts.delete');
            Route::get('update-data', [AccountsController::class, 'updateData'])->name('accounts.updateData');
            Route::get('show/{id:number}', [AccountsController::class, 'getAccount'])->name('accounts.show');
        });

        Route::group(['prefix' => 'assets'], function () {
            Route::post('delete/{id:number}', [AssetsController::class, 'delete'])->name('assets.delete');
            Route::get('edit/{id:number}', [AssetsController::class, 'edit'])->name('assets.edit');
            Route::get('get-by-ticker/{ticker}/{stockMarket?}', [AssetsController::class, 'getByTicker'])->name('assets.getByTicker');
            Route::post('store/{account:number}', [AssetsController::class, 'store'])->name('assets.store');
            Route::post('sell/{id:number}', [AssetsController::class, 'sell'])->name('assets.sell');

            Route::get('sold', [AssetsController::class, 'soldAssets'])->name('assets.sold-assets');
        });

        // Сбережения
        Route::group(['prefix' => 'savings'], function () {
            // Счета
            Route::get('accounts', [SavingAccountsController::class, 'index'])->name('savings.accounts');
            Route::post('accounts/delete/{id:number}', [SavingAccountsController::class, 'delete'])->name('savings.accounts.delete');
            Route::any('accounts/create', [SavingAccountsController::class, 'create'])->name('savings.accounts.create');

            // Операции
            Route::get('/', [SavingsController::class, 'index'])->name('savings.index');
            Route::post('delete/{id:number}', [SavingsController::class, 'delete'])->name('savings.delete');
            Route::post('create', [SavingsController::class, 'create'])->name('savings.create');
        });
    });
});

Route::get('/{any}', fn() => view('spa'))
    ->where('any', '.*')
    ->name('spa');
