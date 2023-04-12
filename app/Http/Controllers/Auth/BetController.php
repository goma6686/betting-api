<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BetController extends Controller
{
    public function index (Request $request){
        if (Auth::user()){
            //$token = DB::table('personal_access_tokens')->select('token')->where('tokenable_id', Auth::user()->id)->first();
            $token = Auth::user()->tokens->first(); //nesamone, TODO later look into https://laravel.com/docs/10.x/http-client#headers , bearer token
            return view('betgames', ['token' => $token]);
        } else {
            return view('betgames', ['token' => '-']);
        }
    }
}
