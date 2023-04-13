<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Auth\BetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [GameController::class, 'index']);
Route::get('/BetGames', [BetController::class, 'index']);

Route::controller(RegisterController::class)->group(function() {
    Route::post('/store', 'store')->name('store');
    Route::get('/register', 'register')->name('register');
});
Route::controller(LoginController::class)->group(function() {
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('/update-balance', [UserController::class, 'update'])->name('update-balance');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/BetGames', [BetController::class, 'index']);
});