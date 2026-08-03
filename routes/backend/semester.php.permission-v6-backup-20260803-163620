<?php


use App\Http\Controllers\backend\SemesterController;
use Illuminate\Support\Facades\Route;


// Semester Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/semester', [SemesterController::class, 'SemesterShow'])->name('semester.show');
    Route::post('/store/semester', [SemesterController::class, 'SemesterStore'])->name('semester.store');
    Route::get('/edit/semester/{id}', [SemesterController::class, 'EditSemester']);
    Route::post('/update/semester', [SemesterController::class, 'UpdateSemester'])->name('semester.update');
    Route::get('/delete/semester/{id}', [SemesterController::class, 'DeleteSemester'])->name('semester.delete');
});
