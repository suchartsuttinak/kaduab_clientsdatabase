<?php

use App\Http\Controllers\backend\ProjectController;
use Illuminate\Support\Facades\Route;

// Project Modal Route All
Route::middleware('auth')->group(function () {

    Route::get('/project', [ProjectController::class, 'ProjectShow'])
        ->name('project.show');

    Route::post('/store/project', [ProjectController::class, 'ProjectStore'])
        ->name('project.store');

    Route::get('/edit/project/{id}', [ProjectController::class, 'EditProject'])
        ->name('project.edit');

    Route::post('/update/project', [ProjectController::class, 'UpdateProject'])
        ->name('project.update');

    Route::delete('/delete/project/{id}', [ProjectController::class, 'DeleteProject'])
        ->name('project.delete');
});