<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface{
    public function getUserById(string $userId): User;
    public function updateBalance(string $userId,string $type,int $balance,int $amount);
    public function manualUserBalance(string $userId,float $amount);
}