<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\TokenRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Http\Traits\TokenTrait;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\TransactionTrait;
use App\Enums\ResponseStatus;
use Illuminate\Support\Str;

class ApiService
{
    use TokenTrait, ResponseTrait, TransactionTrait;

    public function __construct(
        protected TokenRepositoryInterface $tokenRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    public function index($secret, $data){
        $response_errors = $this->apiValidation($secret, $data);

        if(!isset($response_errors) && $data->method !== 'ping'){
            if($data->method === 'transaction_bet_payin'){
                $response_errors = $this->validation($this->getTransactionData($this->getUserByToken($data->token)->id, (int)$this->getUserByToken($data->token)->balance, $data));
                
            } else if($data->method === 'transaction_bet_payout'){
                $response_errors = $this->validation($this->getTransactionData($data->player_id, (int)$this->userRepository->getUserById($data->player_id)->balance, $data));

            }
            $params = $this->apiMethods($data, $response_errors[1] ?? null);
            $this->refreshToken($data->token);
        }

        $response_errors = isset($response_errors) ? $response_errors : $this->generateSuccessResponse();

        $responseId = Str::uuid()->toString();
        $signature = hash_hmac('sha256', $responseId, $secret);

        return [
            'response_errors' => $response_errors[0] ?? $response_errors,
            'params' => $params ?? null, 
            'responseId' => $responseId, 
            'signature' => $signature
        ];
    }

    public function apiValidation(string $secret, $data): ?array{

        if (!($this->checkSignature($secret, $data->requestId, $data->signature))){
            return $this->generateErrorResponse(0, ResponseStatus::WRONG_SIGNATURE, ResponseStatus::$statusText[ResponseStatus::WRONG_SIGNATURE]);

        }else if(!($this->checkTime($data->time))){
            return $this->generateErrorResponse(0, ResponseStatus::REQUEST_EXPIRED, ResponseStatus::$statusText[ResponseStatus::REQUEST_EXPIRED]);

        } else {
            if(!($this->checkToken($data->token, $data->method)) && (($data->method !== 'ping') && ($data->method !== 'transaction_bet_payout'))){
                return $this->generateErrorResponse(0, ResponseStatus::INVALID_TOKEN, ResponseStatus::$statusText[ResponseStatus::INVALID_TOKEN]);
            } else {
                return null;
            }
        }
    }

    function apiMethods($data, $already_processed): ?array{
        $params = [];
        $user = $this->getUserByToken($data->token);

        switch ($data->method) {
            case "get_account_details":
                $params['user_id'] = ($user)['id'];
                $params['username'] = ($user)['username'];
                $params['currency'] = ($user)['currency'];
                $params['info'] = ($this->tokenRepository->getToken($data->token)->token);
                break;

            case 'request_new_token':
                $params['new_token'] =  $data->token;
                break;

            case "get_balance":
                $params['balance'] = ($user)['balance'];
                break;

            case "transaction_bet_payin":
            case "transaction_bet_payout":
                $params['already_processed'] = $already_processed;
                $params['balance_after'] = ($user)['balance'];
                break;
        }

        return $params;
    }

    function checkSignature(string $secret, string $requestId, string $signature): bool{
        return hash_hmac('sha256', $requestId, $secret) === $signature;
    }

    function checkTime(int $time): bool{
        //return time() - $time <= 60;
        return true;
    }
}