<?php

use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentActivityController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentCenterController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentTimelineController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentAssessmentController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentGoalController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentFollowupController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentReportController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentOutcomeController;
use App\Http\Controllers\Frontend\IndividualDevelopment\IndividualDevelopmentSupplementController;
use Illuminate\Support\Facades\Route;

Route::get('individual-development/center', [IndividualDevelopmentCenterController::class, 'index'])->middleware(['auth'])->name('individual-development.center');

Route::prefix('clients/{client}/individual-development')
    ->middleware(['auth'])
    ->whereNumber('client')
    ->name('individual-development.')
    ->group(function () {
        Route::get('/', [IndividualDevelopmentController::class, 'index'])->name('index');
        Route::get('/timeline', [IndividualDevelopmentTimelineController::class, 'index'])->name('timeline');
        Route::get('/create', [IndividualDevelopmentController::class, 'create'])->name('create');
        Route::post('/', [IndividualDevelopmentController::class, 'store'])->name('store');
        Route::get('/edit', [IndividualDevelopmentController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/', [IndividualDevelopmentController::class, 'update'])->name('update');
        Route::match(['put', 'patch'], '/profile', [IndividualDevelopmentController::class, 'updateProfile'])->name('profile.update');
        Route::match(['put', 'patch'], '/support-network', [IndividualDevelopmentSupplementController::class, 'updateSupportNetwork'])->name('support-network.update');
        Route::match(['put', 'patch'], '/discharge-plan', [IndividualDevelopmentSupplementController::class, 'updateDischargePlan'])->name('discharge-plan.update');
        Route::post('/coordinations', [IndividualDevelopmentSupplementController::class, 'storeCoordination'])->name('coordinations.store');
        Route::match(['put', 'patch'], '/coordinations/{coordination}', [IndividualDevelopmentSupplementController::class, 'updateCoordination'])->whereNumber('coordination')->name('coordinations.update');
        Route::delete('/coordinations/{coordination}', [IndividualDevelopmentSupplementController::class, 'destroyCoordination'])->whereNumber('coordination')->name('coordinations.destroy');
        Route::match(['put', 'patch'], '/documents/statuses', [IndividualDevelopmentSupplementController::class, 'updateDocuments'])->name('documents.update');
        Route::delete('/', [IndividualDevelopmentController::class, 'destroy'])->name('destroy');
        Route::get('/close', [IndividualDevelopmentController::class, 'closeForm'])->name('close.form');
        Route::post('/close', [IndividualDevelopmentController::class, 'close'])->name('close');
        Route::post('/cancel', [IndividualDevelopmentController::class, 'cancel'])->name('cancel');

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
        Route::post('/goals/{goal}/achieve', [IndividualDevelopmentGoalController::class, 'achieve'])->whereNumber('goal')->name('goals.achieve');
        Route::post('/goals/{goal}/cancel', [IndividualDevelopmentGoalController::class, 'cancel'])->whereNumber('goal')->name('goals.cancel');
        Route::post('/goals/{goal}/reopen', [IndividualDevelopmentGoalController::class, 'reopen'])->whereNumber('goal')->name('goals.reopen');
        Route::delete('/goals/{goal}', [IndividualDevelopmentGoalController::class, 'destroy'])->whereNumber('goal')->name('goals.destroy');

        Route::get('/goals/{goal}/activities/create', [IndividualDevelopmentActivityController::class, 'create'])->whereNumber('goal')->name('activities.create');
        Route::post('/goals/{goal}/activities', [IndividualDevelopmentActivityController::class, 'store'])->whereNumber('goal')->name('activities.store');
        Route::get('/activities/{activity}/edit', [IndividualDevelopmentActivityController::class, 'edit'])->whereNumber('activity')->name('activities.edit');
        Route::match(['put', 'patch'], '/activities/{activity}', [IndividualDevelopmentActivityController::class, 'update'])->whereNumber('activity')->name('activities.update');
        Route::post('/activities/{activity}/cancel', [IndividualDevelopmentActivityController::class, 'cancel'])->whereNumber('activity')->name('activities.cancel');
        Route::delete('/activities/{activity}', [IndividualDevelopmentActivityController::class, 'destroy'])->whereNumber('activity')->name('activities.destroy');

        Route::get('/followups/create', [IndividualDevelopmentFollowupController::class, 'create'])->name('followups.create');
        Route::post('/followups', [IndividualDevelopmentFollowupController::class, 'store'])->name('followups.store');
        Route::get('/followups/{followup}', [IndividualDevelopmentFollowupController::class, 'show'])->whereNumber('followup')->name('followups.show');
        Route::get('/followups/{followup}/edit', [IndividualDevelopmentFollowupController::class, 'edit'])->whereNumber('followup')->name('followups.edit');
        Route::match(['put', 'patch'], '/followups/{followup}', [IndividualDevelopmentFollowupController::class, 'update'])->whereNumber('followup')->name('followups.update');
        Route::delete('/followups/{followup}', [IndividualDevelopmentFollowupController::class, 'destroy'])->whereNumber('followup')->name('followups.destroy');


        Route::get('/outcomes', [IndividualDevelopmentOutcomeController::class, 'index'])->name('outcomes.index');
        Route::get('/outcomes/create', [IndividualDevelopmentOutcomeController::class, 'create'])->name('outcomes.create');
        Route::post('/outcomes', [IndividualDevelopmentOutcomeController::class, 'store'])->name('outcomes.store');
        Route::get('/outcomes/{assessment}', [IndividualDevelopmentOutcomeController::class, 'show'])->whereNumber('assessment')->name('outcomes.show');
        Route::get('/outcomes/{assessment}/edit', [IndividualDevelopmentOutcomeController::class, 'edit'])->whereNumber('assessment')->name('outcomes.edit');
        Route::match(['put', 'patch'], '/outcomes/{assessment}', [IndividualDevelopmentOutcomeController::class, 'update'])->whereNumber('assessment')->name('outcomes.update');

        Route::get('/reports', [IndividualDevelopmentReportController::class, 'hub'])->name('report.hub');
        Route::get('/reports/progress', [IndividualDevelopmentReportController::class, 'progress'])->name('report.progress');
        Route::get('/reports/summary', [IndividualDevelopmentReportController::class, 'summary'])->name('report.summary');
        Route::get('/report', [IndividualDevelopmentReportController::class, 'show'])->name('report.show');
        Route::get('/report/pdf', [IndividualDevelopmentReportController::class, 'pdf'])->name('report.pdf');
    });
