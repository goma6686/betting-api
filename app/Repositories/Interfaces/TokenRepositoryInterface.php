<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface TokenRepositoryInterface
{
    public function getToken(string $plainToken);
    public function checkToken(string $token): bool;
    public function issueToken (User $user): string;
    public function refreshToken(string $token);
    public function getUserByToken(string $token);
}