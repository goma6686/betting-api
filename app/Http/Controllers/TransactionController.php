<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function store($usr_id, $amount, $tsc_id){

        DB::transaction(function () use ($usr_id, $amount, $tsc_id){
            Transaction::create([
                'user_id' => $usr_id,
                'amount' => $amount,
                'transaction_id' => $tsc_id,
            ]);
        });
    }
}
