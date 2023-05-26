<?php

namespace App\Repositories;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
class TransactionRepository implements TransactionRepositoryInterface
{

    public function doesEntryExist($column, $value, $filter, $operator): bool
    {
        return (Transaction::where($column, $operator, $value)->where('transaction_type', $operator, $filter)->exists());
    }

    public function getTransactionById($transactionId)
    {
        return Transaction::findOrFail($transactionId);
    }

    public function checkBalance($balance, $amount): bool
    {
        return ($balance >= $amount);
    }

    public function createTransaction(array $data)
    {
        Transaction::create([
            "user_id" => $data["user_id"],
            "amount" => $data["amount"],
            "currency" => "eur",
            "bet_id" => $data["bet_id"],
            "transaction_id" => $data["transaction_id"],
            "transaction_type" => $data["transaction_type"]
        ]);
    }
}