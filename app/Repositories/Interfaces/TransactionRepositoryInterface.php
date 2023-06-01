<?php

namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface{
    public function doesTransactionExist($column, $value, $filter, $operator): bool;
    public function getTransactionById($transactionId);
    public function createTransaction(array $transactionData);
    public function checkBalance($balance, $amount): bool;
    public function createTransactionData($user_id, $balance, $requestDTO): array;
}