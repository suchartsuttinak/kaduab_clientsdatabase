<?php

use App\Http\Controllers\Frontend\HealthcareRightController;
use Illuminate\Support\Facades\Route;

Route::prefix('healthcare-rights')
    ->name('healthcare_rights.')
    ->group(function () {
        Route::get('/add/{client_id}', [HealthcareRightController::class, 'index'])
            ->whereNumber('client_id')
            ->name('index');

        Route::post('/store', [HealthcareRightController::class, 'store'])
            ->name('store');

        Route::get('/edit/{id}', [HealthcareRightController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/update/{id}', [HealthcareRightController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/delete/{id}', [HealthcareRightController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');

        Route::get('/report/{client_id}', [HealthcareRightController::class, 'report'])
            ->whereNumber('client_id')
            ->name('report');
    });
