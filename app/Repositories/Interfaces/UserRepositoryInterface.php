<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface{
    public function getUserById($userId);
    public function updateBalance($userId, $type, $balance, $amount);
    public function manualUserBalance($userId, $amount);
}