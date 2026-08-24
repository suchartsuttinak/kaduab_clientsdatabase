<?php

use App\Http\Controllers\Frontend\University\UniversityDashboardController;
use App\Http\Controllers\Frontend\University\UniversityDocumentController;
use App\Http\Controllers\Frontend\University\UniversityEnrollmentController;
use App\Http\Controllers\Frontend\University\UniversityFollowupController;
use App\Http\Controllers\Frontend\University\UniversityOutcomeController;
use App\Http\Controllers\Frontend\University\UniversityReportController;
use App\Http\Controllers\Frontend\University\UniversitySemesterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('university')->name('university.')->group(function () {
    Route::get('/dashboard', [UniversityDashboardController::class, 'index'])->name('dashboard');
    Route::get('/students', [UniversityEnrollmentController::class, 'index'])->name('enrollments.index');

    Route::get('/client/{clientId}', [UniversityEnrollmentController::class, 'client'])->name('client');
    Route::get('/client/{clientId}/create', [UniversityEnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/client/{clientId}', [UniversityEnrollmentController::class, 'store'])->name('enrollments.store');

    Route::get('/enrollments/{id}', [UniversityEnrollmentController::class, 'show'])->name('enrollments.show');
    Route::get('/enrollments/{id}/edit', [UniversityEnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('/enrollments/{id}', [UniversityEnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('/enrollments/{id}', [UniversityEnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    Route::get('/enrollments/{enrollmentId}/semesters/create', [UniversitySemesterController::class, 'create'])->name('semesters.create');
    Route::post('/enrollments/{enrollmentId}/semesters', [UniversitySemesterController::class, 'store'])->name('semesters.store');
    Route::get('/semesters/{id}', [UniversitySemesterController::class, 'show'])->name('semesters.show');
    Route::get('/semesters/{id}/edit', [UniversitySemesterController::class, 'edit'])->name('semesters.edit');
    Route::put('/semesters/{id}', [UniversitySemesterController::class, 'update'])->name('semesters.update');
    Route::delete('/semesters/{id}', [UniversitySemesterController::class, 'destroy'])->name('semesters.destroy');

    Route::get('/semesters/{semesterRecordId}/followups/create', [UniversityFollowupController::class, 'create'])->name('followups.create');
    Route::post('/semesters/{semesterRecordId}/followups', [UniversityFollowupController::class, 'store'])->name('followups.store');
    Route::get('/followups/{id}/edit', [UniversityFollowupController::class, 'edit'])->name('followups.edit');
    Route::put('/followups/{id}', [UniversityFollowupController::class, 'update'])->name('followups.update');
    Route::delete('/followups/{id}', [UniversityFollowupController::class, 'destroy'])->name('followups.destroy');

    Route::get('/enrollments/{enrollmentId}/outcome', [UniversityOutcomeController::class, 'form'])->name('outcomes.form');
    Route::post('/enrollments/{enrollmentId}/outcome', [UniversityOutcomeController::class, 'save'])->name('outcomes.store');
    Route::put('/enrollments/{enrollmentId}/outcome', [UniversityOutcomeController::class, 'save'])->name('outcomes.update');
    Route::delete('/enrollments/{enrollmentId}/outcome', [UniversityOutcomeController::class, 'destroy'])->name('outcomes.destroy');

    Route::post('/semesters/{semesterRecordId}/documents', [UniversityDocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{id}/view', [UniversityDocumentController::class, 'view'])->name('documents.view');
    Route::get('/documents/{id}/download', [UniversityDocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{id}', [UniversityDocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/semesters/{id}/report', [UniversityReportController::class, 'semester'])->name('reports.semester');
    Route::get('/enrollments/{id}/report', [UniversityReportController::class, 'enrollment'])->name('reports.enrollment');
});
