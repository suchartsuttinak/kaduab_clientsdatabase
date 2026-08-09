<?php

use App\Http\Controllers\Frontend\VisitFamilyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('vitsitFamily')
    ->group(function (): void {
        Route::get('/add/{client_id}', [VisitFamilyController::class, 'AddvisitFamily'])
            ->whereNumber('client_id')
            ->name('visitFamily.create');

        Route::post('/store/{client_id}', [VisitFamilyController::class, 'StoreVisitFamily'])
            ->whereNumber('client_id')
            ->name('vitsitFamily.store');

        Route::get('/edit/{id}', [VisitFamilyController::class, 'EditVisitFamily'])
            ->whereNumber('id')
            ->name('vitsitFamily.edit');

        Route::put('/update/{id}', [VisitFamilyController::class, 'UpdateVisitFamily'])
            ->whereNumber('id')
            ->name('vitsitFamily.update');

        Route::get('/report/{id}', [VisitFamilyController::class, 'ReportVisitFamily'])
            ->whereNumber('id')
            ->name('vitsitFamily.report');

        // รูปเยี่ยมบ้านเป็น Private Storage และเปิดผ่าน Controller เท่านั้น
        Route::get('/image/{id}/view', [VisitFamilyController::class, 'viewImage'])
            ->whereNumber('id')
            ->name('vitsitFamily.image.view');

        Route::patch('/image/{id}', [VisitFamilyController::class, 'replaceImage'])
            ->whereNumber('id')
            ->name('image.replace');

        // คง URL เดิมไว้เพื่อไม่ให้กระทบ JavaScript/หน้าเดิม
        Route::delete('/vitsitFamily/image/{id}', [VisitFamilyController::class, 'destroy'])
            ->whereNumber('id')
            ->name('image.destroy');

        Route::get('/get-districts/{province_id}', [VisitFamilyController::class, 'getDistricts'])
            ->whereNumber('province_id')
            ->name('vitsitFamily.getDistricts');

        Route::get('/get-subdistricts/{district_id}', [VisitFamilyController::class, 'getSubdistricts'])
            ->whereNumber('district_id')
            ->name('vitsitFamily.getSubdistricts');

        Route::get('/get-zipcode/{subdistrict_id}', [VisitFamilyController::class, 'getZipcode'])
            ->whereNumber('subdistrict_id')
            ->name('vitsitFamily.getZipcode');
    });
