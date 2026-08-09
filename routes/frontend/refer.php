<?php

use App\Http\Controllers\Frontend\ReferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,executive,social_worker'])
    ->prefix('refer')
    ->group(function (): void {
        Route::get('/refers/all', [ReferController::class, 'allRefers'])
            ->name('refers.all');

        Route::get('/refers/report/{client_id}', [ReferController::class, 'report'])
            ->whereNumber('client_id')
            ->name('refers.report');

        Route::get('/refers/{client_id}', [ReferController::class, 'index'])
            ->whereNumber('client_id')
            ->name('refers.index');

        Route::post('/refers/store', [ReferController::class, 'store'])
            ->name('refers.store');

        Route::put('/refers/{id}/approve', [ReferController::class, 'approve'])
            ->whereNumber('id')
            ->name('refers.approve');

        Route::put('/refers/{id}/restore', [ReferController::class, 'restore'])
            ->whereNumber('id')
            ->name('refers.restore');

        // รายงานการประชุมเป็น Private Storage
        Route::get('/refers/{id}/meeting-report', [ReferController::class, 'viewMeetingReport'])
            ->whereNumber('id')
            ->name('refers.meeting_report.view');
    });
