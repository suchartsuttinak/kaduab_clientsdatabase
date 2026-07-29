<?php

use App\Http\Controllers\Landing\ScholarshipChildController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/scholarship-children/public-report',
    [ScholarshipChildController::class, 'publicReport']
)->name('scholarship.children.public_report');

Route::middleware(['auth'])->group(function () {
    Route::get(
        '/scholarship/children',
        [ScholarshipChildController::class, 'index']
    )->name('scholarship.children.index');

    Route::get(
        '/scholarship/children/report',
        [ScholarshipChildController::class, 'report']
    )->name('scholarship.children.report');

    Route::post(
        '/scholarship/children',
        [ScholarshipChildController::class, 'store']
    )->name('scholarship.children.store');

    /* คำขอรอบใหม่ของบุคคลเดิม */
    Route::post(
        '/scholarship/children/{child}/applications',
        [ScholarshipChildController::class, 'storeApplication']
    )->name('scholarship.children.applications.store');

    Route::put(
        '/scholarship/children/{child}',
        [ScholarshipChildController::class, 'update']
    )->name('scholarship.children.update');

    Route::patch(
        '/scholarship/children/{child}/status',
        [ScholarshipChildController::class, 'updateStatus']
    )->name('scholarship.children.status');

    Route::post(
        '/scholarship/children/{child}/expenses',
        [ScholarshipChildController::class, 'storeExpense']
    )->name('scholarship.children.expenses.store');

    Route::put(
        '/scholarship/children/{child}/expenses/{expense}',
        [ScholarshipChildController::class, 'updateExpense']
    )->name('scholarship.children.expenses.update');

    Route::delete(
        '/scholarship/children/{child}/expenses/{expense}',
        [ScholarshipChildController::class, 'destroyExpense']
    )->name('scholarship.children.expenses.destroy');

    Route::delete(
        '/scholarship/children/{child}',
        [ScholarshipChildController::class, 'destroy']
    )->name('scholarship.children.delete');
});