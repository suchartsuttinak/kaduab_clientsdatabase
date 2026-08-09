<?php


use App\Http\Controllers\backend\DocumentController;
use Illuminate\Support\Facades\Route;

// Document Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/document', [DocumentController::class, 'ShowDocument'])->name('document.show');
    Route::post('/store/document', [DocumentController::class, 'StoreDocument'])->name('document.store');
    Route::get('/edit/document/{id}', [DocumentController::class, 'EditDocument'])->name('document.edit');
    Route::post('/update/document', [DocumentController::class, 'UpdateDocument'])->name('document.update');
    Route::delete('/delete/document/{id}', [DocumentController::class, 'DeleteDocument'])->name('document.delete');
});