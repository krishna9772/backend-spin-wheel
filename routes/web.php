<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


Route::prefix('admin')->group(function () {

    // PUBLIC
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('login', function () {
        return view('admin.login');
    });
    
    // PROTECTED
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', function (Request $request) {

            $user = $request->user(); // currently authenticated via token
            $user->tokens()->delete(); // remove current token



            return redirect('/admin/login');
        })->name('admin.logout');

        Route::get('/rewards', function () {
            return view('admin.rewards');
        });
    });
});

Route::get('/spin', function () {
    return view('spin-wheel');
})->name('spin.view');