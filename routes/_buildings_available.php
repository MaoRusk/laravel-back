<?php

use App\Http\Controllers\BuildingsAvailableController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'buildings/{building}/availability',
    'as' => 'api.buildings.availability.',
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('/', [BuildingsAvailableController::class, 'index'])->name('index');
    Route::post('/', [BuildingsAvailableController::class, 'store'])->name('store');
    Route::get('/{buildingAvailable}', [BuildingsAvailableController::class, 'show'])->name('show');
    Route::put('/{buildingAvailable}', [BuildingsAvailableController::class, 'update'])->name('update');
    Route::delete('/{buildingAvailable}', [BuildingsAvailableController::class, 'destroy'])->name('destroy');
    Route::put('/{buildingAvailable}/to-absorption', [BuildingsAvailableController::class, 'toAbsorption'])->name('to-absorption');
});

