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
    /*
     * เพิ่มผู้รับบริการ/เปลี่ยนสถานะ:
     * ใช้สิทธิ์รายฟอร์มเป็นตัวกำหนด view/create/update สำหรับทุกบทบาทที่ไม่ใช่ Admin
     */
    Route::group([], function () {

        Route::get('/client/add', [ClientController::class, 'clientAdd'])
            ->name('client.add');

        Route::post('/client/store', [ClientController::class, 'ClientStore'])
            ->name('client.store');

        Route::post('/client/change-status/{id}', [ClientController::class, 'changeStatus'])
            ->name('client.changeStatus');
    });

    /*
     * เปิด GET หน้าแก้ไขให้ผู้ใช้ที่มีสิทธิ์ view เข้าดูได้แบบ read-only
     * และให้ EnforceFormPermission / CheckFormPermission เป็นผู้ตัดสินสิทธิ์ update
     * แทนการบล็อกทั้งหน้าด้วย role middleware
     */
    Route::get('/client/edit/{id}', [ClientController::class, 'ClientEdit'])
        ->name('client.edit');

    Route::post('/client/update', [ClientController::class, 'ClientUpdate'])
        ->name('client.update');

   
        // TRANSFER CASE
        // =====================================================
        // UNIFIED_ACCESS_SCOPE_V5: ใช้ permission + Project/House scope ไม่ผูกกับชื่อบทบาท
        Route::group([], function () {

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
            Route::group([], function () {

                Route::delete('/client/delete/{id}', [ClientController::class, 'ClientDelete'])
                    ->name('client.delete');
            });
        });