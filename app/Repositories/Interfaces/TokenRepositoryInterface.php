<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface TokenRepositoryInterface
{
    public function getToken(string $plainToken);
    public function checkToken(string $token): bool;
    public function issue_token (User $user): string;
    public function refreshToken(string $token);
}