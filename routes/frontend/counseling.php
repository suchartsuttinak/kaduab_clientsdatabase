<?php

use App\Http\Controllers\Frontend\CounselingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('counseling')
    ->name('counseling.')
    ->controller(CounselingController::class)
    ->group(function () {

        Route::get('/add/{client_id}', 'index')
            ->whereNumber('client_id')
            ->name('index');

        Route::post('/store', 'store')
            ->name('store');

        Route::get('/show/{id}', 'show')
            ->whereNumber('id')
            ->name('show');

        Route::get('/edit/{id}', 'edit')
            ->whereNumber('id')
            ->name('edit');

        Route::put('/update/{id}', 'update')
            ->whereNumber('id')
            ->name('update');

        Route::delete('/delete/{id}', 'destroy')
            ->whereNumber('id')
            ->name('delete');

        Route::get('/{id}/round/create', 'createRound')
            ->whereNumber('id')
            ->name('followup.create');

        Route::post('/round/store', 'storeRound')
            ->name('followup.store');

        Route::get('/round/edit/{id}', 'editRound')
            ->whereNumber('id')
            ->name('followup.edit');

        Route::put('/round/update/{id}', 'updateRound')
            ->whereNumber('id')
            ->name('followup.update');

        Route::delete('/round/delete/{id}', 'destroyRound')
            ->whereNumber('id')
            ->name('followup.delete');

        Route::get('/{id}/round/{round}/report', 'roundReport')
            ->whereNumber('id')
            ->whereNumber('round')
            ->name('followup.report');

        Route::get('/report/{id}', 'report')
            ->whereNumber('id')
            ->name('report');
    });
