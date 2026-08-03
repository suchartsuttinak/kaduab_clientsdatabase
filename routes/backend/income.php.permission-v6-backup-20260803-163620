<?php


use App\Http\Controllers\backend\IncomeController;
use Illuminate\Support\Facades\Route;

// Income Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/income', [IncomeController::class, 'ShowIncome'])->name('income.show');
    Route::post('/store/income', [IncomeController::class, 'StoreIncome'])->name('income.store');
    Route::get('/edit/income/{id}', [IncomeController::class, 'EditIncome']);
    Route::post('/update/income', [IncomeController::class, 'UpdateIncome'])->name('income.update');
    Route::get('/delete/income/{id}', [IncomeController::class, 'DeleteIncome'])->name('income.delete');
});