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
use Laravel\Sanctum\PersonalAccessToken;
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
        $response_errors = $this->api_validation($secret, $data);

        if(!isset($response_errors) && $data->method !== 'ping'){
            $params = $this->api_methods($data);
        }

        if(!isset($response_errors)){
            $response_errors = $this->generateSuccessResponse();
            if($data->method !== 'ping'){
                $this->refresh_token($data->token);
            }
        }

        $responseId = Str::uuid()->toString();
        $signature = hash_hmac('sha256', $responseId, $secret);

        return [
            'response_errors' =>$response_errors, 
            'params' => $params ?? null, 
            'responseId' => $responseId, 
            'signature' => $signature
        ];
    }

    public function api_validation(string $secret, $data): ?array{

        if (!($this->check_signature($secret, $data->requestId, $data->signature))){
            return $this->generateErrorResponse(0, ResponseStatus::WRONG_SIGNATURE, ResponseStatus::$statusText[ResponseStatus::WRONG_SIGNATURE]);

        }else if(!($this->check_time($data->time))){
            return $this->generateErrorResponse(0, ResponseStatus::REQUEST_EXPIRED, ResponseStatus::$statusText[ResponseStatus::REQUEST_EXPIRED]);

        } else {
            if(!($this->checkToken($data->token, $data->method)) && (($data->method !== 'ping') && ($data->method !== 'transaction_bet_payout'))){
                return $this->generateErrorResponse(0, ResponseStatus::INVALID_TOKEN, ResponseStatus::$statusText[ResponseStatus::INVALID_TOKEN]);
            } else {
                return null;
            }
        }
    }

    function api_methods($data): ?array{
        $params = [];
        $user = $this->get_user_by_token($data->token);

        switch ($data->method) {
            case "get_account_details":
                $params['user_id'] = ($user)['id'];
                $params['username'] = ($user)['username'];
                $params['currency'] = ($user)['currency'];
                $params['info'] = ($this->token($data->token)->token);
                break;

            case 'request_new_token':
                $params['new_token'] =  $data->token;
                break;

            case "get_balance":
                $params['balance'] = ($user)['balance'];
                break;

            case "transaction_bet_payin":
            case "transaction_bet_payout":
                $transac_data = $this->getTransactionData($user['id'], $user['balance'], $data);
                $res = $this->validation($transac_data);
                
                if(isset($res[0])){
                    $res = $this->generateErrorResponse($res[0][0], $res[0][1], $res[0][2]);
                }
                $params['already_processed'] = $res[1];
                $params['balance_after'] = PersonalAccessToken::findToken($data->token)->tokenable['balance'];
                break;
        }

        return $params;
    }

    function check_signature(string $secret, string $requestId, string $signature): bool{
        return hash_hmac('sha256', $requestId, $secret) === $signature;
    }

    function check_time(int $time): bool{
        //return time() - $time <= 60;
        return true;
    }
}