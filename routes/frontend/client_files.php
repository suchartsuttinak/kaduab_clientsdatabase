<?php

use App\Http\Controllers\Frontend\ClientFileController;
use Illuminate\Support\Facades\Route;

Route::prefix('clients/{client_id}')
    ->where(['client_id' => '[0-9]+'])
    ->controller(ClientFileController::class)
    ->group(function (): void {
        Route::get('files', 'index')
            ->name('client_files.index');

        Route::get('files/create', 'create')
            ->name('client_files.create');

        Route::post('files', 'store')
            ->name('client_files.store');

        Route::get('files/{file}/view', 'view')
            ->whereNumber('file')
            ->name('client_files.view');

        Route::get('files/{file}/download', 'download')
            ->whereNumber('file')
            ->name('client_files.download');

        Route::delete('files/{file}', 'destroy')
            ->whereNumber('file')
            ->name('client_files.destroy');
    });
