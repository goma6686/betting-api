<?php

namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface{

    public function getUserById($userId) 
    {
        return User::findOrFail($userId);
    }

public function updateBalance($userId, $type, $balance, $amount) 
    {
        $user = $this->getUserById($userId);

        $type === 'payin' ? 
            $user->update(['balance' => $balance - $amount]) : 
            $user->update(['balance' => $balance += $amount]);
    }

    public function manualUserBalance($userId, $amount){
       $user = $this->getUserById($userId);

        $user->update(['balance' => 100*$amount]);

    }
}