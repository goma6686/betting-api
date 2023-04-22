<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;

class BetController extends UserController
{
    public function index (){
        return view('betgames', ['token' => UserController::issuetoken(Auth::user())]);
    }
}