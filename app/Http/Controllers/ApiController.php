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
        $req_array = XmlRequest::xml_request($request->getContent());

        if (!($this->check_signature(self::SECRET, $req_array->requestId, $req_array->signature))){
            $response_errors = $this->error_msg("0", "1", "wrong signature");

            if (!($this->check_time($req_array->time))){
                $response_errors = $this->error_msg("0", "2", "request is expired");
            }
        } else {
            $response_errors = $this->error_msg("1", "0", "");
        }

        if($req_array->method !== 'ping'){
            $usr_c = new UserController();
            if($usr_c->check_token($req_array->token)){
                $token = PersonalAccessToken::findToken($req_array->token);

                switch($req_array->method){
                    case "get_balance":
                        $usr_c->refresh_token($req_array->token);
                        break;

                    case "get_account_details":
                        $info['id'] = ($token->tokenable)['id'];
                        $info['username'] = ($token->tokenable)['username'];
                        $info['currency'] = ($token->tokenable)['currency'];
                        $info['token'] = ($token->token);
                        $usr_c->refresh_token($req_array->token);
                        break;

                    case "refresh_token":
                        $usr_c->refresh_token($req_array->token);
                        break;

                    case 'request_new_token':
                        if($usr_c->check_token($req_array->token)){
                            $response_errors = $this->error_msg("1", "0", "");
                        } else {
                            $response_errors = $this->error_msg("0", "3", "invalid token");
                        }
                        break;

                    case "transaction_bet_payin":
                        if(Schema::hasTable('transactions')){
                            
                            if(Transaction::where('transaction_id', '=', $req_array->transactionId)->where('transaction_type', '=', 'payin')->exists()){
                                $usr_c->refresh_token($req_array->token);
                                $info['already_processed'] = 1;
    
                            } else if(($token->tokenable)['balance'] >= $req_array->amount) {
                                (new TransactionController)->payin_payout(($token->tokenable)['id'], $req_array->amount, $req_array->betId, $req_array->transactionId, 'payin');
                                $info['already_processed'] = 0;
                                $usr_c->refresh_token($req_array->token);
                            } else {
                                $response_errors = $this->error_msg("0", "703", "insufficient balance");

                            };
                        }
                        break;

                    case "transaction_bet_payout":
                        if(Schema::hasTable('transactions')){
                            if(Transaction::where('transaction_id', '=', $req_array->transactionId)
                            ->where('transaction_type', '=', 'payout')
                            ->exists()){

                                $usr_c->refresh_token($req_array->token);
                                $info['already_processed'] = 1;
    
                            } else if(Transaction::where('bet_id', '=', $req_array->betId)->where('transaction_type', '=', 'payin')->exists()) {
                                if(Transaction::where('transaction_id', '<>', $req_array->transactionId)
                                ->where('transaction_type', '=', 'payout')
                                ->where('bet_id', '=', $req_array->betId)
                                ->exists()){
                                    $usr_c->refresh_token($req_array->token);
                                    $info['already_processed'] = 1;
                                } else {
                                    (new TransactionController)->payin_payout(($token->tokenable)['id'], $req_array->amount, $req_array->betId, $req_array->transactionId, 'payout');
                                    $info['already_processed'] = 0;
                                    $usr_c->refresh_token($req_array->token);
                                }
                            } else {
                                $response_errors = $this->error_msg("0", "700", "there is no PAYIN with provided bet_id");
                            };
                        }
                        break;

                    $response_errors = $this->error_msg("1", "0", "");
                }
            } else {
                $response_errors = $this->error_msg("0", "3", "invalid token");
            }
        }

        return response(
            (XmlResponse::xml_response($req_array->method, $req_array->token, $response_errors, $info ?? null, self::SECRET))
            ->toXmlString())
            ->header('Content-Type', 'application/xml');
    }

    function check_signature($secret, $requestId, $signature){
        return hash_hmac('sha256', $requestId, $secret) === $signature ? true : false;
    }

    function check_time($time){
        //return time() - $time <= 60 ? true : false;
        return true;
    }

    function error_msg($success_code, $code, $text){
        $response['success'] = $success_code;
        $response['error_code'] = $code;
        $response['error_text'] = $text;
        return $response;
    }
}
