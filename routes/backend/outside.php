<?php


use App\Http\Controllers\backend\OutsideController;
use Illuminate\Support\Facades\Route;

// Outside Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/outside', [OutsideController::class, 'ShowOutside'])->name('outside.show');
    Route::post('/store/outside', [OutsideController::class, 'StoreOutside'])->name('outside.store');
    Route::get('/edit/outside/{id}', [OutsideController::class, 'EditOutside'])->name('outside.edit');
    Route::post('/update/outside', [OutsideController::class, 'UpdateOutside'])->name('outside.update');
    Route::delete('/delete/outside/{id}', [OutsideController::class, 'DeleteOutside'])->name('outside.delete');
});