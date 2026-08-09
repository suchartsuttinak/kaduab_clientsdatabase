<?php

use App\Http\Controllers\Landing\ScholarshipChildController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get(
        '/scholarship/children',
        [ScholarshipChildController::class, 'index']
    )->name('scholarship.children.index');

    Route::get(
        '/scholarship/children/report',
        [ScholarshipChildController::class, 'report']
    )->name('scholarship.children.report');


    Route::get(
        '/scholarship/children/{child}/photo',
        [ScholarshipChildController::class, 'viewPhoto']
    )->name('scholarship.children.photo');

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


    Route::get(
        '/scholarship/children/{child}/expenses/{expense}/attachments/{attachment}',
        [ScholarshipChildController::class, 'viewAttachment']
    )->name('scholarship.children.attachments.view');

    Route::delete(
        '/scholarship/children/{child}/expenses/{expense}',
        [ScholarshipChildController::class, 'destroyExpense']
    )->name('scholarship.children.expenses.destroy');

    Route::delete(
        '/scholarship/children/{child}',
        [ScholarshipChildController::class, 'destroy']
    )->name('scholarship.children.delete');
});