<?php


use App\Http\Controllers\backend\CitizenshipController;
use Illuminate\Support\Facades\Route;

// citizens Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/citizenship', [CitizenshipController::class, 'ShowCitizenships'])->name('citizenship.show');
    Route::post('/store/citizenship', [CitizenshipController::class, 'StoreCitizenship'])->name('citizenship.store');
    Route::get('/edit/citizenship/{id}', [CitizenshipController::class, 'EditCitizenship']);
    Route::post('/update/citizenship', [CitizenshipController::class, 'UpdateCitizenship'])->name('citizenship.update');
    Route::get('/delete/citizenship/{id}', [CitizenshipController::class, 'DeleteCitizenship'])->name('citizenship.delete');
});