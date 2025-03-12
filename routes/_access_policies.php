<?php

use App\Http\Controllers\AccessPolicyController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'access-policies',
    'as' => 'api.access-policies.',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', [AccessPolicyController::class, 'index'])->name('index');
    Route::post('/', [AccessPolicyController::class, 'store'])->name('store');
    Route::get('/{id}', [AccessPolicyController::class, 'show'])->name('show');
    Route::put('/{id}', [AccessPolicyController::class, 'update'])->name('update');
    Route::delete('/{id}', [AccessPolicyController::class, 'destroy'])->name('destroy');
});
