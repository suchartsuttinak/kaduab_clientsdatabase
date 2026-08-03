<?php


use App\Http\Controllers\backend\TranslateController;
use Illuminate\Support\Facades\Route;

// translates Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/translate', [TranslateController::class, 'ShowTranslate'])->name('translate.show');
    Route::post('/store/translate', [TranslateController::class, 'StoreTranslate'])->name('translate.store');
    Route::get('/edit/translate/{id}', [TranslateController::class, 'EditTranslate']);
    Route::post('/update/translate', [TranslateController::class, 'UpdateTranslate'])->name('translate.update');
    Route::get('/delete/translate/{id}', [TranslateController::class, 'DeleteTranslate'])->name('translate.delete');
});