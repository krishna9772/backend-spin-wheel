<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('rewards', function() { return view('admin.rewards'); });
    Route::get('spins', function() { return view('admin.spins'); });
});

Route::post('/spin', [SpinController::class, 'spin']);
