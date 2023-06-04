<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Events\UpdateBalance;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface{

    public function getUserById(string $userId): User
    {
        return User::findOrFail($userId);
    }

    public function updateBalance(string $userId,string $type,int $balance,int $amount)//User, ir is user id bei balance
    {
        $user = $this->getUserById($userId);

        $type === 'payin' ? 
            $user->update(['balance' => $balance - $amount]) : 
            $user->update(['balance' => $balance + $amount]);
            
        event(new UpdateBalance($balance));
    }

    public function manualUserBalance(string $userId,float $amount){
       $user = $this->getUserById($userId);
       
        $user->update(['balance' => 100*$amount]);
    }
}