<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function updateBalance(Request $request)
    {
        $request->validate([
            'balance' => 'numeric|between:0.0,50000.99',
        ]);

        DB::table('users')
              ->where('id', Auth::id())
              ->update(['balance' => 100*($request->balance)]);
        
        return redirect()->back();
    }

    public function placeBet(){
        //
    }

}
