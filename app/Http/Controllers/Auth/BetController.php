<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class BetController extends Controller
{
    public function index (){
        return view('betgames', ['token' => $this->issuetoken(Auth::user())]);
    }

    public function issuetoken (User $user){
        if (request()->user()->tokens()){
            request()->user()->tokens()->delete();
        }
        return $user->createToken('token')->plainTextToken;
    }
}
