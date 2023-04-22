<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
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

    protected static function issuetoken (User $user){
        if ($user->tokens()){
            $user->tokens()->delete();
        }
        return $user->createToken('token')->plainTextToken;
    }

    public static function check_token($sactumToken){
        return (
            PersonalAccessToken::findToken($sactumToken) && //does it exist
            PersonalAccessToken::findToken($sactumToken)['created_at']->addMinutes(config('sanctum.expiration'))->gte(now()) //has it expired
        ) ? true : false;
    }

    function refresh_token($sactumToken){
        DB::table('personal_access_tokens')->where('id', PersonalAccessToken::findToken($sactumToken)['id'])->update(['created_at' => now()]);
    }

    public function placeBet(){
        //
    }

}
