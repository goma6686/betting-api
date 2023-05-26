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

    public function issue_token(User $user): string
    {
        if ($user->tokens()){
            $user->tokens()->delete();
        }
        return $user->createToken('token')->plainTextToken;
    }

    public function checkToken(string $token): bool{
        return (
            $this->getToken($token) &&
            $this->isExpired($this->getToken($token)->created_at));
    }

    public function isExpired($created_at): bool{
        return $created_at->addMinutes(config('sanctum.expiration'))->gte(Carbon::now());
    }

    public function refreshToken(string $token){
        DB::table('personal_access_tokens')->where('id', PersonalAccessToken::findToken($token)->id)->update(['created_at' => now()]);
    }
}