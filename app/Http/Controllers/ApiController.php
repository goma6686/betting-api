<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Transaction;
use App\Traits\XmlResponse;
use App\Traits\XmlRequest;
use Illuminate\Support\Facades\DB;

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
            if($this->check_token($req_array['token'])){

                switch($req_array['method']){
                    case "get_balance":
                    case "get_account_details":
                        $info = PersonalAccessToken::findToken($req_array['token'])->tokenable; //TODO check expiration
                        break;

                    case "transaction_bet_payin":
                        if(Transaction::where('transaction_id', '=', $req_array['transaction_id'])){
                            //
                        };
                        break;
                }
                $this->refresh_token($req_array['token']);
                $response_errors = $this->error_msg("1", "0", "");

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

    function check_token($sactumToken){
        return (
            PersonalAccessToken::findToken($sactumToken) && //does it exist
            PersonalAccessToken::findToken($sactumToken)['created_at']->addMinutes(config('sanctum.expiration'))->gte(now()) //has it expired
        ) ? true : false;
    }

    function refresh_token($sactumToken){
        DB::table('personal_access_tokens')->where('id', PersonalAccessToken::findToken($sactumToken)['id'])->update(['created_at' => now()]);
    }

    function error_msg($success_code, $code, $text){
        $response['success'] = $success_code;
        $response['error_code'] = $code;
        $response['error_text'] = $text;
        return $response;
    }
}
