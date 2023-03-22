<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Auth\RegisterLoginController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Authenticate;

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
Route::get('/BetGames', function () {
    return view('betgames');
});

Route::controller(RegisterLoginController::class)->group(function() {

    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');

    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::post('/logout', 'logout')->name('logout');
});

Route::group(['middleware' => 'auth'], function(){
    Route::post('/update-balance', [UserController::class, 'update'])->name('update-balance');
});