<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use Carbon\Carbon;
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

    public function issuetoken (User $user){
        if ($user->tokens()){
            $user->tokens()->delete();
        }
        return $user->createToken('token')->plainTextToken;
    }

    public function check_token(string $sactumToken): bool
    {
        return (
            PersonalAccessToken::findToken($sactumToken) && //does it exist
            (PersonalAccessToken::findToken($sactumToken)->created_at)->addMinutes(config('sanctum.expiration'))->gte(Carbon::now()) //has it expired
        );
    }

    public function refresh_token(string $sactumToken){
        DB::table('personal_access_tokens')->where('id', PersonalAccessToken::findToken($sactumToken)->id)->update(['created_at' => now()]);
    }
}
