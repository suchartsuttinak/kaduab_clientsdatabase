<?php

use App\Http\Controllers\Frontend\EstimateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('estimate')
    ->group(function (): void {
        Route::get('/show/{client_id}', [EstimateController::class, 'ShowEstimate'])
            ->whereNumber('client_id')
            ->name('estimate.show');

        Route::post('/store', [EstimateController::class, 'StoreEstimate'])
            ->name('estimate.store');

        Route::get('/edit/{id}', [EstimateController::class, 'EditEstimate'])
            ->whereNumber('id')
            ->name('estimate.edit');

        Route::put('/update/{id}', [EstimateController::class, 'UpdateEstimate'])
            ->whereNumber('id')
            ->name('estimate.update');

        Route::delete('/delete/{id}', [EstimateController::class, 'DeleteEstimate'])
            ->whereNumber('id')
            ->name('estimate.delete');

        Route::get('/report/{id}', [EstimateController::class, 'ReportEstimate'])
            ->whereNumber('id')
            ->name('estimate.report');

        // รูปประกอบการประเมินเป็น Private Storage
        Route::get('/image/{id}/view', [EstimateController::class, 'viewImage'])
            ->whereNumber('id')
            ->name('estimate.image.view');

        // แก้ path เดิมที่ซ้ำ prefix (/estimate/estimate/check-duplicate)
        Route::get('/check-duplicate', [EstimateController::class, 'CheckDuplicate'])
            ->name('estimate.check-duplicate');
    });
