<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;

class TransactionController extends Controller
{
    public function store($usr_id, $amount, $bet_id, $tsc_id, $tsc_type){

        DB::transaction(function () use ($usr_id, $amount, $bet_id, $tsc_id, $tsc_type){
            Transaction::create([
                'user_id' => $usr_id,
                'amount' => $amount,
                'currency' => 'eur',
                'bet_id' => $bet_id,
                'transaction_id' => $tsc_id,
                'transaction_type' => $tsc_type
            ]);
        });
    }

    public function payin_payout($usr_id, $amount, $bet_id, $tsc_id, $tsc_type){
        DB::transaction(function() use ($usr_id, $amount, $bet_id, $tsc_id, $tsc_type) {
            $user = User::findOrFail($usr_id);
            ($tsc_type == 'payin') ? $user->balance -= $amount : $user->balance += $amount;
            $user->save();

            $this->store($usr_id, $amount, $bet_id, $tsc_id, $tsc_type);
        });
    }
}
