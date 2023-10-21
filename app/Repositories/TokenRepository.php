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

    public function issueToken(User $user): string
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
        if($this->checkToken($token)){
            DB::table('personal_access_tokens')->where('id', PersonalAccessToken::findToken($token)->id)->update(['created_at' => now()]);
        }
    }

    public function getUserByToken(string $token){
        return $this->getToken($token)->tokenable;
    }
}