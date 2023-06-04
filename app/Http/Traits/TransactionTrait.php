<?php
declare(strict_types=1);

namespace App\Http\Traits;

use App\Enums\ResponseStatus;

trait TransactionTrait{      
    public function validation($data): ?array{
        if($this->doesTransactionExist("transaction_id", $data["transaction_id"], $data["transaction_type"], '=')){
            return [$this->generateSuccessResponse(), 1];
        } else {
            if($data["transaction_type"] === 'payin'){
                return $this->payinValidation($data);
            } else {
                return $this->payoutValidation($data);
            }
        }
    }

    public function payinValidation($data): array{
        if (!($this->transactionRepository->checkBalance($data["user_balance"], $data["amount"]))){
            return [$this->generateErrorResponse(0, ResponseStatus::INSUFFICIENT_BALANCE, ResponseStatus::$statusText[ResponseStatus::INSUFFICIENT_BALANCE]), 0];
        } else {
            $this->payinPayout($data);
            return [$this->generateSuccessResponse(), 0];
        }
    }

    public function payoutValidation($data): array{
        if(!($this->transactionRepository->doesTransactionExist("bet_id", $data["bet_id"], "payin", '='))){
            return [$this->generateErrorResponse(0, ResponseStatus::NO_PAYIN, ResponseStatus::$statusText[ResponseStatus::NO_PAYIN]), 0];
        } else {
            if(($this->doesTransactionExist("bet_id", $data["bet_id"],  $data["transaction_type"], '=') && ($this->doesTransactionExist("transaction_id", $data["transaction_id"], $data["transaction_type"], '<>')))){
                return [$this->generateSuccessResponse(), 1];
            } else {
                $this->payinPayout($data);
                return [$this->generateSuccessResponse(), 0];
            }
        }
    }

    public function doesTransactionExist($column, $value, $type, $operator): bool{
        return $this->transactionRepository->doesTransactionExist($column, $value, $type, $operator);
    }

    public function payinPayout(array $data){
        $this->userRepository->updateBalance((string)$data["user_id"], $data["transaction_type"], (int)$data["user_balance"], (int)$data["amount"]);
        $this->createTransaction($data);
    }

    public function createTransaction($data){
        $this->transactionRepository->createTransaction($data);
    }

    public function getTransactionData(string $user_id, int $balance, $requestDTO){
        return $this->transactionRepository->createTransactionData($user_id, $balance, $requestDTO);
    }
}