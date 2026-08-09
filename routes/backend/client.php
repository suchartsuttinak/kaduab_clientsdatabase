<?php

use App\Http\Controllers\backend\ClientController;
use App\Http\Controllers\Frontend\ClientTransferController;
use Illuminate\Support\Facades\Route;

// =====================================================
// CLIENT ROUTE
// =====================================================
Route::middleware('auth')->group(function () {

    // =====================================================
    // รายการผู้รับบริการปัจจุบันของหน่วยงาน
    // =====================================================
    Route::get('/client', [ClientController::class, 'clientShow'])
        ->name('client.show');

    // รูปผู้รับบริการเป็นข้อมูลส่วนบุคคล: ให้ผ่าน authorization ก่อนทุกครั้ง
    Route::get('/client/{id}/image', [ClientController::class, 'ClientImage'])
        ->whereNumber('id')
        ->name('client.image');

    // =====================================================
    // ทะเบียนกลางเคสทั้งหมด
    // admin เห็นทุกโปรเจ็ค
    // user เห็นเฉพาะที่มีสิทธิ์
    // =====================================================
    Route::get('/client/cases', [ClientController::class, 'ClientShowRefer'])
    ->name('client.cases');

    // =====================================================
    // AJAX ADDRESS
    // =====================================================
    Route::get('/get-districts/{province_id}', [ClientController::class, 'getDistricts']);
    Route::get('/get-subdistricts/{district_id}', [ClientController::class, 'getSubdistricts']);
    Route::get('/get-zipcode/{subdistrict_id}', [ClientController::class, 'getZipcode']);

    Route::get('/get-origin-districts/{province_id}', [ClientController::class, 'getOriginDistricts']);
    Route::get('/get-origin-subdistricts/{district_id}', [ClientController::class, 'getOriginSubdistricts']);
    Route::get('/get-origin-zipcode/{subdistrict_id}', [ClientController::class, 'getOriginZipcode']);

    // =====================================================
    // CREATE / EDIT CLIENT
    // =====================================================
    Route::middleware('role:admin,executive,social_worker')->group(function () {

        Route::get('/client/add', [ClientController::class, 'clientAdd'])
            ->name('client.add');

        Route::post('/client/store', [ClientController::class, 'ClientStore'])
            ->name('client.store');

        Route::get('/client/edit/{id}', [ClientController::class, 'ClientEdit'])
            ->name('client.edit');

        Route::post('/client/update', [ClientController::class, 'ClientUpdate'])
            ->name('client.update');

        Route::post('/client/change-status/{id}', [ClientController::class, 'changeStatus'])
            ->name('client.changeStatus');
    });

   
        // TRANSFER CASE
        // =====================================================
        Route::middleware('role:admin')->group(function () {

            Route::get('/client/transfers', [ClientTransferController::class, 'index'])
                ->name('client.transfers');

            Route::get('/client/transfer/{client}', [ClientTransferController::class, 'create'])
                ->name('client.transfer.create');

            Route::post('/client/transfer/store', [ClientTransferController::class, 'store'])
                ->name('client.transfer.store');

            Route::put('/client/transfer/{id}/approve', [ClientTransferController::class, 'approve'])
                ->name('client.transfer.approve');

            Route::put('/client/transfer/{id}/reject', [ClientTransferController::class, 'reject'])
                ->name('client.transfer.reject');
        });

            // =====================================================
            // DELETE
            // =====================================================
            Route::middleware('role:admin')->group(function () {

                Route::delete('/client/delete/{id}', [ClientController::class, 'ClientDelete'])
                    ->name('client.delete');
            });
        });