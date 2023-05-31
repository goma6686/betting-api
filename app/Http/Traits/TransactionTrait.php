<?php

namespace App\Http\Traits;

trait TransactionTrait{      
    public function validation($data){
        $is_processed = 0;
        
        if($this->does_exist("transaction_id", $data["transaction_id"], $data["transaction_type"], '=')){
            $is_processed = 1;
        } else {
            if($data["transaction_type"] === 'payin'){
                $this->transactionRepository->payin($data) ? 
                    $this->payin_payout($data) 
                    : $response_errors = array("0", "703", "insufficient balance");
            } else {
                $this->transactionRepository->payout($data) 
                    ? : $response_errors = array("0", "700", "there is no PAYIN with provided bet_id");

                isset($response_errors) ??
                    ($this->does_exist("bet_id", $data["bet_id"],  $data["transaction_type"], '=') && !($this->does_exist("transaction_id", $data["transaction_id"], $data["transaction_type"], '=')))
                    ? $is_processed = 1 
                    : $this->payin_payout($data);
            }
        }
        return array($response_errors, $is_processed);
    }

    public function does_exist($column, $value, $type, $operator){
        return $this->transactionRepository->doesTransactionExist($column, $value, $type, $operator);
    }

    public function payin_payout(array $data){
        $this->userRepository->updateBalance($data["user_id"], $data["transaction_type"], $data["user_balance"], $data["amount"]);
        $this->transactionRepository->createTransaction($data);
    }

    public function getTransactionData($user_id, $balance, $requestDTO){
        return $this->transactionRepository->create_transaction_data($user_id, $balance, $requestDTO);
    }
}