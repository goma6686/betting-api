<?php

namespace App\Http\Traits;

use App\Models\User;

trait TokenTrait{

    public function checkToken($token){
        return $this->tokenRepository->checkToken($token);
    }

    public function issue_token(User $user): string{
        return $this->tokenRepository->issue_token($user);
    }

    public function token($plainToken){
        return $this->tokenRepository->getToken($plainToken);
    }

    public function refresh_token(string $sactumToken){
        return $this->tokenRepository->refreshToken($sactumToken);
    }

    public function get_user_by_token($token){
        return $this->token($token)->tokenable;
    }
}