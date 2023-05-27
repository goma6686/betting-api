<?php

namespace App\Repositories;

use App\Events\UpdateBalance;
use App\Models\User;
use Pusher\Pusher;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface{

    public function getUserById($userId): User
    {
        return User::findOrFail($userId);
    }

    public function getUserBalance($user) {

        return $user->balance;
    }

    public function updateBalance($userId, $type, $balance, $amount) 
    {
        $user = $this->getUserById($userId);

        $type === 'payin' ? 
            $user->update(['balance' => $balance - $amount]) : 
            $user->update(['balance' => $balance + $amount]);
            
        event(new UpdateBalance($user->balance));
    }

    public function manualUserBalance($userId, $amount){
       $user = $this->getUserById($userId);

        $user->update(['balance' => 100*$amount]);
    }
}