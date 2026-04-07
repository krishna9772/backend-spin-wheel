<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpinController;
use App\Http\Controllers\RewardController;


Route::post('/spin', [SpinController::class, 'spin'])->name('spin.api');
Route::get('/spins', [SpinController::class, 'getAdminSpins']);

Route::get('/admin/rewards', [RewardController::class, 'index']);

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    
        Route::post('/rewards', [RewardController::class, 'store']);
        Route::put('/rewards/{id}', [RewardController::class, 'update']);
        Route::delete('/rewards/{id}', [RewardController::class, 'destroy']);
        Route::post('/rewards/{id}/refill', [RewardController::class, 'refill']);

});





