<?php

namespace App\Http\Traits;

use App\Models\User;

trait TokenTrait{

    public function checkToken($token){
        return $this->tokenRepository->checkToken($token);
    }

    public function issueToken(User $user): string{
        return $this->tokenRepository->issueToken($user);
    }

    public function token($plainToken){
        return $this->tokenRepository->getToken($plainToken);
    }

    public function refreshToken(string $sactumToken){
        return $this->tokenRepository->refreshToken($sactumToken);
    }

    public function getUserByToken($token){
        return $this->token($token)->tokenable;
    }
}