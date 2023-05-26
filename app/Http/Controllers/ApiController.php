<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\XmlResponse;
use App\DTO\XmlRequest;
use App\Http\Traits\TokenTrait;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\TransactionTrait;
use App\Repositories\Interfaces\TokenRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
class ApiController extends UserController
{
    
    use TokenTrait, ResponseTrait, TransactionTrait;
    
    private const SECRET = "CCHWS-ZIFJV-HEAOB-DV336";

    public function __construct(
        protected TokenRepositoryInterface $tokenRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    public function methods(Request $request){
        $requestDTO = XmlRequest::xml_request($request->getContent());
        

        if (!($this->check_signature(self::SECRET, $requestDTO->requestId, $requestDTO->signature))){
            $response_errors = $this->generateErrorResponse("0", "1", "wrong signature");

        }else if(!($this->check_time($requestDTO->time))){
            $response_errors = $this->generateErrorResponse("0", "2", "request is expired");
        } else {
            
            if($this->checkToken($requestDTO->token)){
                $user = $this->get_user_by_token($requestDTO->token);

                switch ($requestDTO->method) {
                    case "get_account_details":
                        $info['user_id'] = ($user)['id'];
                        $info['username'] = ($user)['username'];
                        $info['currency'] = ($user)['currency'];
                        $info['info'] = ($this->token($requestDTO->token)->token);
                        break;

                    case 'request_new_token':
                        $info['new_token'] =  $requestDTO->token;
                        break;

                    case "get_balance":
                        $info['balance'] = ($user)['balance'];
                        break;

                    case "transaction_bet_payin":
                    case "transaction_bet_payout":
                        $transac_data = $this->getTransactionData($user['id'], $user['balance'], $requestDTO);
                        $res = $this->validation($transac_data, null);
                        if(isset($res[0])){
                            $response_errors = $this->generateErrorResponse($res[0][0], $res[0][1], $res[0][2]);
                        }
                        $info['already_processed'] = $res[1];
                        break;
                }
            } else {
                if($requestDTO->method !== 'ping'){
                    $response_errors = $this->generateErrorResponse("0", "3", "invalid token");
                }
            }
            if(!isset($response_errors)){
                $response_errors = $this->generateSuccessResponse();
                if($requestDTO->method !== 'ping'){
                    $this->refresh_token($requestDTO->token);
                }
            }
        }
        return response(
            (XmlResponse::xml_response($requestDTO->method, $requestDTO->token, $response_errors, $info ?? null, self::SECRET))->toXmlString()
            )->header('Content-Type', 'application/xml');
    }

    function check_signature(string $secret, string $requestId, string $signature): bool{
        return hash_hmac('sha256', $requestId, $secret) === $signature;
    }

    function check_time(int $time): bool{
        //return time() - $time <= 60;
        return true;
    }
}
