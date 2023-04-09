<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BetController extends Controller
{
    public function index (){
        if (Auth::user()){
            return view('betgames', ['token' => $this->issuetoken(Auth::user())]);
        } else {
            return view('betgames', ['token' => '-']);
        }
    }

    public function issuetoken (User $user){
        return $user->createToken('token')->plainTextToken;
    }
}
