<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpinController;

Route::post('/spin', [SpinController::class, 'spin']);
