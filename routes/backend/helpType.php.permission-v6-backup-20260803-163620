<?php

use App\Http\Controllers\backend\HelpTypeController;
use Illuminate\Support\Facades\Route;

// Help Type Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/help_type', [HelpTypeController::class, 'ShowHelpType'])->name('help_type.show');
    Route::post('/store/help_type', [HelpTypeController::class, 'StoreHelpType'])->name('help_type.store');
    Route::get('/edit/help_type/{id}', [HelpTypeController::class, 'EditHelpType']);
    Route::post('/update/help_type', [HelpTypeController::class, 'UpdateHelpType'])->name('help_type.update');
    Route::get('/delete/help_type/{id}', [HelpTypeController::class, 'DeleteHelpType'])->name('help_type.delete');
});