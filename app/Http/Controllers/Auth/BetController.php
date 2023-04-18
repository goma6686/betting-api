<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BetController extends Controller
{
    public function index (){
        return view('betgames', ['token' => User::issuetoken(Auth::user())]);
    }
}