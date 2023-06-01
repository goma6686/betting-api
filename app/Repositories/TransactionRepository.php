<?php

namespace App\Repositories;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
class TransactionRepository implements TransactionRepositoryInterface
{

    public function doesTransactionExist($column, $value, $filter, $operator): bool
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

    function createTransactionData($user_id, $balance, $requestDTO): array{
        $transaction_data = array(
            "user_id" => $user_id, 
            "user_balance" => $balance, 
            "amount" => $requestDTO->amount, 
            "bet_id" => $requestDTO->betId, 
            "transaction_id" => $requestDTO->transactionId,
            "transaction_type" => ($requestDTO->method === "transaction_bet_payin") ? 'payin' : 'payout');
        return $transaction_data;
    }
}