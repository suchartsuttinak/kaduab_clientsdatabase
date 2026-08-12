<?php

use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentActivityController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentAssessmentController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentGoalController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentFollowupController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('clients/{client}/individual-development')
    ->middleware(['auth'])
    ->whereNumber('client')
    ->name('individual-development.')
    ->group(function () {
        Route::get('/', [IndividualDevelopmentController::class, 'index'])->name('index');
        Route::get('/create', [IndividualDevelopmentController::class, 'create'])->name('create');
        Route::post('/', [IndividualDevelopmentController::class, 'store'])->name('store');

        Route::get('/baseline/create', [IndividualDevelopmentAssessmentController::class, 'create'])->name('baseline.create');
        Route::post('/baseline', [IndividualDevelopmentAssessmentController::class, 'store'])->name('baseline.store');
        Route::get('/baseline', [IndividualDevelopmentAssessmentController::class, 'show'])->name('baseline.show');
        Route::get('/baseline/edit', [IndividualDevelopmentAssessmentController::class, 'edit'])->name('baseline.edit');
        Route::match(['put', 'patch'], '/baseline', [IndividualDevelopmentAssessmentController::class, 'update'])->name('baseline.update');

        Route::get('/goals', [IndividualDevelopmentGoalController::class, 'index'])->name('goals.index');
        Route::get('/goals/create', [IndividualDevelopmentGoalController::class, 'create'])->name('goals.create');
        Route::post('/goals', [IndividualDevelopmentGoalController::class, 'store'])->name('goals.store');
        Route::get('/goals/{goal}/edit', [IndividualDevelopmentGoalController::class, 'edit'])->whereNumber('goal')->name('goals.edit');
        Route::match(['put', 'patch'], '/goals/{goal}', [IndividualDevelopmentGoalController::class, 'update'])->whereNumber('goal')->name('goals.update');
        Route::delete('/goals/{goal}', [IndividualDevelopmentGoalController::class, 'destroy'])->whereNumber('goal')->name('goals.destroy');

        Route::get('/goals/{goal}/activities/create', [IndividualDevelopmentActivityController::class, 'create'])->whereNumber('goal')->name('activities.create');
        Route::post('/goals/{goal}/activities', [IndividualDevelopmentActivityController::class, 'store'])->whereNumber('goal')->name('activities.store');
        Route::get('/activities/{activity}/edit', [IndividualDevelopmentActivityController::class, 'edit'])->whereNumber('activity')->name('activities.edit');
        Route::match(['put', 'patch'], '/activities/{activity}', [IndividualDevelopmentActivityController::class, 'update'])->whereNumber('activity')->name('activities.update');
        Route::delete('/activities/{activity}', [IndividualDevelopmentActivityController::class, 'destroy'])->whereNumber('activity')->name('activities.destroy');

        Route::get('/followups/create', [IndividualDevelopmentFollowupController::class, 'create'])->name('followups.create');
        Route::post('/followups', [IndividualDevelopmentFollowupController::class, 'store'])->name('followups.store');
        Route::get('/followups/{followup}', [IndividualDevelopmentFollowupController::class, 'show'])->whereNumber('followup')->name('followups.show');
        Route::get('/followups/{followup}/edit', [IndividualDevelopmentFollowupController::class, 'edit'])->whereNumber('followup')->name('followups.edit');
        Route::match(['put', 'patch'], '/followups/{followup}', [IndividualDevelopmentFollowupController::class, 'update'])->whereNumber('followup')->name('followups.update');
        Route::delete('/followups/{followup}', [IndividualDevelopmentFollowupController::class, 'destroy'])->whereNumber('followup')->name('followups.destroy');

        Route::get('/report', [IndividualDevelopmentReportController::class, 'show'])->name('report.show');
        Route::get('/report/pdf', [IndividualDevelopmentReportController::class, 'pdf'])->name('report.pdf');
    });
