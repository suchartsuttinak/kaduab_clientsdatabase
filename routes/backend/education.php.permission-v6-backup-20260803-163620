<?php

use App\Http\Controllers\backend\EducationController;
use Illuminate\Support\Facades\Route;

// Education Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/education', [EducationController::class, 'ShowEducation'])->name('education.show');
    Route::post('/store/education', [EducationController::class, 'StoreEducation'])->name('education.store');
    Route::get('/edit/education/{id}', [EducationController::class, 'EditEducation']);
    Route::post('/update/education', [EducationController::class, 'UpdateEducation'])->name('education.update');
    Route::get('/delete/education/{id}', [EducationController::class, 'DeleteEducation'])->name('education.delete');
});