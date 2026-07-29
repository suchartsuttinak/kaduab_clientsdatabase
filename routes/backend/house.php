<?php

use App\Http\Controllers\backend\HouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| House Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/house', [HouseController::class, 'HouseShow'])
        ->name('house.show');

    Route::post('/store/house', [HouseController::class, 'HouseStore'])
        ->name('house.store');

    Route::get('/edit/house/{id}', [HouseController::class, 'EditHouse'])
        ->name('house.edit');

    Route::post('/update/house', [HouseController::class, 'UpdateHouse'])
        ->name('house.update');

    Route::delete('/delete/house/{id}', [HouseController::class, 'DeleteHouse'])
        ->name('house.delete');
});