<?php

namespace App\Http\Traits;

trait TransactionTrait{      
    public function validation($data, $response_errors){
        //$info['already_processed'] = 0;
        $info = 0;
        
        if($this->validate_transaction("transaction_id", $data["transaction_id"], $data["transaction_type"], '=')){
            //$info['already_processed'] = 1;
            $info = 1;
        } else {
            if($data["transaction_type"] === 'payin'){
                if($this->check_balance($data["user_balance"], $data["amount"])){
                    $this->payin_payout($data);
                } else {
                    $response_errors =  /*$this->generateErrorResponse*/array("0", "703", "insufficient balance");
                }
            } else {
                if($this->validate_transaction("bet_id", $data["bet_id"], "payin", '=')){
                    if($this->validate_transaction("bet_id", $data["bet_id"],  $data["transaction_type"], '=') && !($this->validate_transaction("transaction_id", $data["transaction_id"], $data["transaction_type"], '='))){
                        //$info['already_processed'] = 1;
                        $info = 1;
                    } else {
                        $this->payin_payout($data);
                    }
                } else {
                    $response_errors =  /*$this->generateErrorResponse*/array("0", "700", "there is no PAYIN with provided bet_id");
                }
            }
        }
        return array($response_errors, $info);
    }

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