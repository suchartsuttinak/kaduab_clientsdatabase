<?php

use App\Http\Controllers\Frontend\HealthcHeckupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,executive,social_worker'])
    ->prefix('healthc-checkups')
    ->group(function (): void {
        Route::get('/', [HealthcHeckupController::class, 'index'])
            ->name('healthc_heckups.index');

        Route::post('/store', [HealthcHeckupController::class, 'store'])
            ->name('healthc_heckups.store');

        Route::get('/edit-json/{id}', [HealthcHeckupController::class, 'editJson'])
            ->whereNumber('id')
            ->name('healthc_heckups.edit_json');

        Route::put('/update/{id}', [HealthcHeckupController::class, 'update'])
            ->whereNumber('id')
            ->name('healthc_heckups.update');

        Route::delete('/delete/{id}', [HealthcHeckupController::class, 'destroy'])
            ->whereNumber('id')
            ->name('healthc_heckups.delete');

        Route::get('/report', [HealthcHeckupController::class, 'report'])
            ->name('healthc_heckups.report');

        // เอกสารทางการแพทย์เป็น Private Storage
        Route::get('/document/{id}/view', [HealthcHeckupController::class, 'viewMedicalDocument'])
            ->whereNumber('id')
            ->name('healthc_heckups.document.view');
    });
