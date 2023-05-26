<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface TokenRepositoryInterface
{
    public function getToken(string $plainToken);
    public function checkToken($token);
    public function issue_token (User $user);
    public function refreshToken(string $sactumToken);
}