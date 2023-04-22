<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Transaction;
use App\Traits\XmlResponse;
use App\Traits\XmlRequest;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

class ApiController extends Controller
{
    private const TOKEN_EXPIRATION_TIME = 1;
    private const SECRET = "CCHWS-ZIFJV-HEAOB-DV336";

    use XmlResponse, XmlRequest;

    public function methods(Request $request){
        $req_array = $this->xml_request($request->getContent());

        if (!($this->check_signature(self::SECRET, $req_array['requestId'], $req_array['signature']))){
            $response_errors = $this->error_msg("0", "1", "wrong signature");

            if (!($this->check_time($req_array['time']))){
                $response_errors = $this->error_msg("0", "2", "request is expired");
            }
        } else {
            $response_errors = $this->error_msg("1", "0", "");
        }

        if($req_array['method'] !== 'ping'){
            $usr_c = new UserController();

            if($usr_c->check_token($req_array['token'])){
                $token = PersonalAccessToken::findToken($req_array['token']);

                switch($req_array['method']){
                    case "get_balance":
                        $usr_c->refresh_token($req_array['token']);

                    case "get_account_details":
                        $info['id'] = ($token->tokenable)['id'];
                        $info['username'] = ($token->tokenable)['username'];
                        $info['currency'] = ($token->tokenable)['currency'];
                        $info['info'] = ($token['token']);
                        $usr_c->refresh_token($req_array['token']);
                        break;

                    case "transaction_bet_payin":
                        if(Schema::hasTable('transactions')){
                            
                            if(Transaction::where('transaction_id', '=', $req_array['transaction_id'])->exists()){
                                $usr_c->refresh_token($req_array['token']);
                                $info['already_processed'] = 1;
    
                            } else if(($token->tokenable)['balance'] >= $req_array['amount']) {
                                $usr_c->refresh_token($req_array['token']);
                                $usr_c->placeBet(($token->tokenable)['id'],  ($token->tokenable)['balance'] - $req_array['amount']);
                                (new TransactionController)->store(($token->tokenable)['id'], ($token->tokenable)['balance'] - $req_array['amount'], $req_array['bet_id'], $req_array['transaction_id']);
                                $info['already_processed'] = 0;

                            } else {
                                $response_errors = $this->error_msg("0", "703", "insufficient balance");

                            };
                        }
                        break;
                    $response_errors = $this->error_msg("1", "0", "");
                }
            } else {
                $response_errors = $this->error_msg("0", "3", "invalid token");
            }
        }

        return response((
            $this->xml_response($req_array['method'], $req_array['token'], $response_errors, $info ?? null, self::SECRET))
                ->asXML())
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
