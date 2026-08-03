<?php


use App\Http\Controllers\backend\PsychoController;
use Illuminate\Support\Facades\Route;

// Psycho Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/psycho', [PsychoController::class, 'ShowPsycho'])->name('psycho.show');
    Route::post('/store/psycho', [PsychoController::class, 'StorePsycho'])->name('psycho.store');
    Route::get('/edit/psycho/{id}', [PsychoController::class, 'EditPsycho']);
    Route::post('/update/psycho', [PsychoController::class, 'UpdatePsycho'])->name('psycho.update');
    Route::get('/delete/psycho/{id}', [PsychoController::class, 'DeletePsycho'])->name('psycho.delete');

});