<?php


use App\Http\Controllers\backend\SubjectController;
use Illuminate\Support\Facades\Route;


// Subject Modal Route All
    Route::middleware('auth')->group(function () {
    Route::get('/subject', [SubjectController::class, 'SubjectShow'])->name('subject.show');
    Route::post('/store/subject', [SubjectController::class, 'SubjectStore'])->name('subject.store');
    Route::get('/edit/subject/{id}', [SubjectController::class, 'EditSubject']);
    Route::post('/update/subject', [SubjectController::class, 'UpdateSubject'])->name('subject.update');
    Route::get('/delete/subject/{id}', [SubjectController::class, 'DeleteSubject'])->name('subject.delete');
});

