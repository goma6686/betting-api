<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Auth\BetController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;

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

Route::controller(RegisterController::class)->group(function() {
    Route::post('/store', 'store')->name('store');
    Route::get('/register', 'create')->name('register');
});

Route::controller(LoginController::class)->group(function() {
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('/update-balance', [UserController::class, 'updateBalance'])->name('update-balance');
    Route::get('/BetGames/{game_id?}', function (Request $request, int $game_id = 7) {
        return view('betgames', ['token' =>  ($request->user()->createToken('token'))->plainTextToken, 'game_id' => $game_id]);
    })->name('BetGames');

});
