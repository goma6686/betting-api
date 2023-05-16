<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Transaction;
use App\DTO\XmlResponse;
use App\DTO\XmlRequest;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

class ApiController extends Controller
{
    private const SECRET = "CCHWS-ZIFJV-HEAOB-DV336";

    public function methods(Request $request){
        $requestDTO = XmlRequest::xml_request($request->getContent());

        if (!($this->check_signature(self::SECRET, $requestDTO->requestId, $requestDTO->signature))){
            $response_errors = $this->generateErrorResponse("0", "1", "wrong signature");

        }else if(!($this->check_time($requestDTO->time))){
            $response_errors = $this->generateErrorResponse("0", "2", "request is expired");
        } else {
            if($requestDTO->method !== "ping"){
                $usr_c = new UserController();

                if($usr_c->check_token($requestDTO->token)){
                    $token = PersonalAccessToken::findToken($requestDTO->token);

                    switch ($requestDTO->method) {
                        case "get_account_details":
                            $info['id'] = ($token->tokenable)['id'];
                            $info['username'] = ($token->tokenable)['username'];
                            $info['currency'] = ($token->tokenable)['currency'];
                            $info['token'] = ($token->token);
                            $usr_c->refresh_token($requestDTO->token);
                            break;

                        case "refresh_token":
                            $info['player_balance'] = ($token->tokenable)['balance'];
                            $usr_c->refresh_token($requestDTO->token);
                            break;

                        case 'request_new_token':
                            if ($usr_c->check_token($requestDTO->token)) {
                                $response_errors = $this->generateSuccessResponse();
                            } else {
                                $response_errors = $this->generateErrorResponse("0", "3", "invalid token");
                            }
                            break;

                        case "get_balance":
                            $usr_c->refresh_token($requestDTO->token);
                            break;

                        case "transaction_bet_payin":
                            if (Schema::hasTable('transactions')) {

                                if (Transaction::where('transaction_id', '=', $requestDTO->transactionId)
                                ->where('transaction_type', '=', 'payin')->exists()) {
                                    $info['already_processed'] = 1;

                                } else if (($token->tokenable)['balance'] >= $requestDTO->amount) {
                                    (new TransactionController)->payin_payout(($token->tokenable)['id'], $requestDTO->amount, $requestDTO->betId, $requestDTO->transactionId, 'payin');
                                    $info['already_processed'] = 0;

                                } else {
                                    $response_errors =  $this->generateErrorResponse("0", "703", "insufficient balance");
                                }
                                $usr_c->refresh_token($requestDTO->token);
                            }
                            break;
                        
                        case "transaction_bet_payout":
                            if(Schema::hasTable('transactions')){
                                if(Transaction::where('transaction_id', '=', $requestDTO->transactionId)
                                ->where('transaction_type', '=', 'payout')
                                ->exists()){
                                    $info['already_processed'] = 1;

                                } else if(Transaction::where('bet_id', '=', $requestDTO->betId)->where('transaction_type', '=', 'payin')->exists()) {
                                    if(Transaction::where('transaction_id', '<>', $requestDTO->transactionId)
                                    ->where('transaction_type', '=', 'payout')
                                    ->where('bet_id', '=', $requestDTO->betId)
                                    ->exists()){
                                        $info['already_processed'] = 1;

                                    } else {
                                        (new TransactionController)->payin_payout(($token->tokenable)['id'], $requestDTO->amount, $requestDTO->betId, $requestDTO->transactionId, 'payout');
                                        $info['already_processed'] = 0;
                                    }
                                } else {
                                    $response_errors = $this->generateErrorResponse("0", "700", "there is no PAYIN with provided bet_id");
                                }
                                $usr_c->refresh_token($requestDTO->token);
                            }
                            break;

                        default:
                        $response_errors = $this->generateSuccessResponse();
                        break;
                    }
                } else {
                    $response_errors = $this->generateErrorResponse("0", "3", "invalid token");
                }
            } else {
                $response_errors = $this->generateSuccessResponse();
            }
            
        }
        
        return response(
            (XmlResponse::xml_response($requestDTO->method, $requestDTO->token, $response_errors, $info ?? null, self::SECRET))
                ->toXmlString())
            ->header('Content-Type', 'application/xml');
    }

    function check_signature(string $secret, string $requestId, string $signature): bool{
        return hash_hmac('sha256', $requestId, $secret) === $signature;
    }

    function check_time(int $time): bool{
        return time() - $time <= 60;
    }

    private function generateErrorResponse(int $success_code, int $code, string $text)
    {
        $response['success'] = $success_code;
        $response['error_code'] = $code;
        $response['error_text'] = $text;
        return $response;
    }

    private function generateSuccessResponse()
    {
        return $this->generateErrorResponse("1", "0", "");
    }
}
