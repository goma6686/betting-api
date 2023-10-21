<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\TokenRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\TransactionTrait;
use App\Enums\ResponseStatus;
use Illuminate\Support\Str;

class ApiService
{
    use ResponseTrait, TransactionTrait;


    public function __construct(
        protected TokenRepositoryInterface $tokenRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function index($secret, $data){
        $response_errors = $this->apiValidation($secret, $data);

        if(!isset($response_errors) && $data->method !== 'ping'){
            if($data->method === 'transaction_bet_payin'){
                $user = $this->tokenRepository->getUserByToken($data->token);
                $response_errors = $this->validation($this->getTransactionData($user->id, (int)$user->balance, $data));
                
            } else if($data->method === 'transaction_bet_payout'){
                $user = $this->userRepository->getUserById($data->player_id);
                $response_errors = $this->validation($this->getTransactionData($data->player_id, (int)$user->balance, $data));

            }
            $params = $this->apiMethods($user ?? null, $data, $response_errors[1] ?? null);
            
            $this->tokenRepository->refreshToken($data->token);
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
            if(!($this->tokenRepository->checkToken($data->token, $data->method)) && (($data->method !== 'ping') && ($data->method !== 'transaction_bet_payout'))){
                return $this->generateErrorResponse(0, ResponseStatus::INVALID_TOKEN, ResponseStatus::$statusText[ResponseStatus::INVALID_TOKEN]);
            } else {
                return null;
            }
        }
    }

    function apiMethods($user, $data, $already_processed): ?array{
        $params = [];

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
                $user = $this->tokenRepository->getUserByToken($data->token);
                $params['balance'] = ($user)['balance'];
                break;

            
            case "transaction_bet_payin":
                $user = $this->tokenRepository->getUserByToken($data->token);
            case "transaction_bet_payout":
                $user ?? $this->userRepository->getUserById($data->player_id);
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
        return time() - $time <= 60;
        //return true;
    }
}