<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BetController extends Controller
{
    public function index (){
        if (Auth::user()){
            return view('betgames', ['token' => $this->issuetoken(Auth::user())->plainTextToken]);
        } else {
            $token = "-";
        }
        return view('betgames')->with('token', $token);
    }

    public function issuetoken (User $user){
        return $user->createToken('token');
    }
}
