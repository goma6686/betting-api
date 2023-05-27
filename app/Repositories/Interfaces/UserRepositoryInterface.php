<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface{
    public function getUserById($userId): User;
    public function getUserBalance($user);
    public function updateBalance($userId, $type, $balance, $amount);
    public function manualUserBalance($userId, $amount);
}