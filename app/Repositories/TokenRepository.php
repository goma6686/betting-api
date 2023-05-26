<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\TokenRepositoryInterface;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TokenRepository implements TokenRepositoryInterface
{
    public function getToken(string $plainToken) 
    {
        return PersonalAccessToken::findToken($plainToken);
    }

    public function issue_token(User $user)
    {
        if ($user->tokens()){
            $user->tokens()->delete();
        }
        return $user->createToken('token')->plainTextToken;
    }

    public function checkToken($token): bool{
        return (
            $this->getToken($token) && //does it exist
            ($this->getToken($token)->created_at)->addMinutes(config('sanctum.expiration'))->gte(Carbon::now()) //has it expired
        );
    }

    public function refreshToken( $sactumToken){
        DB::table('personal_access_tokens')->where('id', PersonalAccessToken::findToken($sactumToken)->id)->update(['created_at' => now()]);
    }
}