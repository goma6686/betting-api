<?php

namespace App\Http\Traits;

trait TransactionTrait{                       
    public function validate_transaction ($column, $value, $type, $operator){
        return $this->does_exist($column, $value, $type, $operator);
    }

    public function does_exist($column, $value, $type, $operator){
        return $this->transactionRepository->doesEntryExist($column, $value, $type, $operator);
    }

    public function check_balance($balance, $amount): bool{
        return $this->transactionRepository->checkBalance($balance, $amount);
    }

    public function payin_payout(array $data){
        $this->userRepository->updateBalance($data["user_id"], $data["transaction_type"], $data["user_balance"], $data["amount"]);
        $this->transactionRepository->createTransaction($data);
    }
}