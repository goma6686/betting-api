<?php

namespace App\Http\Traits;

use App\Models\User;

trait TokenTrait{

    public function checkToken(string $token): bool{
        return $this->tokenRepository->checkToken($token);
    }

    public function issueToken(User $user): string{
        return $this->tokenRepository->issueToken($user);
    }

    public function refreshToken(string $sactumToken){
        return $this->tokenRepository->refreshToken($sactumToken);
    }

    public function getUserByToken(string $token){
        return $this->tokenRepository->getToken($token)->tokenable;
    }
}