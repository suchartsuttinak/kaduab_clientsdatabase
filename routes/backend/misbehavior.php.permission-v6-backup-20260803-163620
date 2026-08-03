<?php


use App\Http\Controllers\backend\MisbehaviorController;
use Illuminate\Support\Facades\Route;

// Misbehavior Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/misbehavior', [MisbehaviorController::class, 'ShowMisbehavior'])->name('misbehavior.show');
    Route::post('/store/misbehavior', [MisbehaviorController::class, 'StoreMisbehavior'])->name('misbehavior.store');
    Route::get('/edit/misbehavior/{id}', [MisbehaviorController::class, 'EditMisbehavior']);
    Route::post('/update/misbehavior', [MisbehaviorController::class, 'UpdateMisbehavior'])->name('misbehavior.update');
    Route::get('/delete/misbehavior/{id}', [MisbehaviorController::class, 'DeleteMisbehavior'])->name('misbehavior.delete');

});