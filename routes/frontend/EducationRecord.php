<?php

use App\Http\Controllers\Frontend\EducationRecordController;
use Illuminate\Support\Facades\Route;

// Education Record Route All
Route::middleware('auth')->group(function () {
    Route::get('/education_record/add/{client_id}',
        [EducationRecordController::class, 'EducationRecordAdd']
    )->name('education_record.add');

    Route::post('/education_record/store',
        [EducationRecordController::class, 'EducationRecordStore']
    )->name('education_record.store');

    Route::get('/education_record/show/{client_id}',
        [EducationRecordController::class, 'EducationRecordShow']
    )->name('education_record_show_legacy');

    // รายงานผลการเรียนทั้งหมดของ client
    Route::get('/education_record/report/{client_id}',
        [EducationRecordController::class, 'EducationRecordReport']
    )->name('education_record.report');

    // ✅ รายงานเฉพาะรายการตาม id
    Route::get('/education_record/report-by-id/{id}',
        [EducationRecordController::class, 'EducationRecordReportById']
    )->name('education_record.report_by_id');

    Route::get('/education_record/edit/{id}',
        [EducationRecordController::class, 'EducationRecordEdit']
    )->name('education_record.edit');

    Route::post('/education_record/update/{id}',
        [EducationRecordController::class, 'EducationRecordUpdate']
    )->name('education_record_update_legacy');
});

// 📚 บันทึกผลการเรียน
Route::prefix('education-record')->middleware('auth')->group(function () {
    Route::get('/add/{client_id}',
        [EducationRecordController::class, 'EducationRecordAdd']
    )->name('education_record_add');

    Route::post('/store',
        [EducationRecordController::class, 'EducationRecordStore']
    )->name('education_record_store');

    Route::get('/{client_id}',
        [EducationRecordController::class, 'EducationRecordShow']
    )->name('education_record_show');

    // รายงานผลการเรียนทั้งหมดของ client
    Route::get('/report/{client_id}',
        [EducationRecordController::class, 'EducationRecordReport']
    )->name('education_record_report');

    // ✅ รายงานเฉพาะรายการตาม id
    Route::get('/report-by-id/{id}',
        [EducationRecordController::class, 'EducationRecordReportById']
    )->name('education_record_report_by_id');

    Route::get('/edit/{id}',
        [EducationRecordController::class, 'EducationRecordEdit']
    )->name('education_record_edit');

    Route::put('/update/{id}',
        [EducationRecordController::class, 'EducationRecordUpdate']
    )->name('education_record_update');

    Route::delete('/delete/{id}',
        [EducationRecordController::class, 'EducationRecordDelete']
    )->name('education_record_delete');
});