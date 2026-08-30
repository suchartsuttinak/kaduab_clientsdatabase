<?php

use App\Http\Controllers\Frontend\ObserveController;
use Illuminate\Support\Facades\Route;

// Routes สำหรับพฤติกรรม (Observe)
Route::prefix('observe')->group(function () {
    Route::get('/add/{client_id}', [ObserveController::class, 'AddObserve'])->name('observe.create');
    Route::post('/store', [ObserveController::class, 'StoreObserve'])->name('observe.store');
    Route::get('/edit/{id}', [ObserveController::class, 'EditObserve'])->name('observe.edit');
    Route::put('/update/{id}', [ObserveController::class, 'UpdateObserve'])->name('observe.update');
    Route::delete('/delete/{id}', [ObserveController::class, 'DeleteObserve'])->name('observe.delete');

       // ✅ เพิ่ม route รายงาน
    Route::get('/report/{id}', [ObserveController::class, 'ReportObserve'])->name('observe.report');

    // Followup ของ observe
    Route::post('/followup/store', [ObserveController::class, 'StoreFollowup'])->name('observe.followup.store');
    Route::get('/followup/edit/{id}', [ObserveController::class, 'EditFollowup'])->name('observe.followup.edit');
    Route::put('/followup/update/{id}', [ObserveController::class, 'UpdateFollowup'])->name('observe.followup.update');
    Route::delete('/followup/delete/{id}', [ObserveController::class, 'DeleteFollowup'])->name('observe.followup.delete');

    // การช่วยเหลือหลังส่งต่อ: นักสังคมสงเคราะห์ / ผู้บริหาร / Admin เท่านั้น (ตรวจซ้ำใน Controller)
    Route::post('/referral/store', [ObserveController::class, 'StoreReferralRound'])->name('observe.referral.store');
    Route::put('/referral/update/{id}', [ObserveController::class, 'UpdateReferralRound'])->name('observe.referral.update');
    Route::delete('/referral/delete/{id}', [ObserveController::class, 'DeleteReferralRound'])->name('observe.referral.delete');
    Route::get('/referral/report/{id}', [ObserveController::class, 'ReportReferral'])->name('observe.referral.report');
});