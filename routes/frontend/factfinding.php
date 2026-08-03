<?php


use App\Http\Controllers\Frontend\FactfindingController;
use Illuminate\Support\Facades\Route;

// Facfiding Route All
    Route::middleware('auth')->group(function () {
    // เพิ่ม factfinding ใหม่ โดยส่ง client_id
    Route::get('/factfinding/add/{client_id}', [FactfindingController::class, 'FactfindingAdd'])
        ->name('factfinding.add');

    // บันทึก factfinding
    Route::post('/factfinding/store', [FactfindingController::class, 'FactfindingStore'])
        ->name('factfinding.store');

    // แก้ไข factfinding
       Route::get('/factfinding/edit/{factfinding_id}', [FactfindingController::class, 'FactfindingEdit'])
    ->name('factfinding.edit');

    // บันทึก factfinding
   Route::post('/factfinding/update/{factfinding_id}', [FactfindingController::class, 'FactfindingUpdate'])
     ->name('factfinding.update');

     Route::delete('/factfinding/{id}', [FactfindingController::class, 'FactfindingDelete'])
    ->name('factfinding.delete');
});

