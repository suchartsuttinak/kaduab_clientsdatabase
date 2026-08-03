<?php


use App\Http\Controllers\backend\CitizenController;
use Illuminate\Support\Facades\Route;

// citizens Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/citizen', [CitizenController::class, 'ShowCitizens'])->name('citizen.show');
    Route::post('/store/citizen', [CitizenController::class, 'StoreCitizen'])->name('citizen.store');
    Route::get('/edit/citizen/{id}', [CitizenController::class, 'EditCitizen']);
    Route::post('/update/citizen', [CitizenController::class, 'UpdateCitizen'])->name('citizen.update');
    Route::get('/delete/citizen/{id}', [CitizenController::class, 'DeleteCitizen'])->name('citizen.delete');
});