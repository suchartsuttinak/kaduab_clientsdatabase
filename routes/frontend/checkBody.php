<?php


use App\Http\Controllers\Frontend\CheckBodyController;
use App\Http\Controllers\SpecialChildReportController; // SPECIAL_CHILDREN_REPORT_V1
use Illuminate\Support\Facades\Route;


// SPECIAL_CHILDREN_REPORT_V1
Route::get('/special-children', [SpecialChildReportController::class, 'index'])
    ->middleware('auth')
    ->name('special_children.index');

// 🏫 ตรวจสุขภาพเบื้องต้น
    Route::prefix('check_body')->name('check_body.')->group(function () {
    Route::get('/add/{client_id}', [CheckBodyController::class, 'CheckBodyAdd'])->name('add');
    Route::post('/store', [CheckBodyController::class, 'CheckBodyStore'])->name('store');
    Route::get('/edit/{id}', [CheckBodyController::class, 'CheckBodyEdit'])->name('edit');
    Route::put('/update/{id}', [CheckBodyController::class, 'CheckBodyUpdate'])->name('update');
    Route::delete('/delete/{id}', [CheckBodyController::class, 'CheckBodyDelete'])->name('delete');
    Route::get('/report/{id}', [CheckBodyController::class, 'CheckBodyReport'])->name('report');
});