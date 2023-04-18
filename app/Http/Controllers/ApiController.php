<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Traits\XmlResponse;
use App\Traits\XmlRequest; 

class ApiController extends Controller
{
    private const TOKEN_EXPIRATION_TIME = 1;

    use XmlResponse, XmlRequest;

    public function method(Request $request){
        $req_array = $this->xml_request($request->getContent());

        $secret = "CCHWS-ZIFJV-HEAOB-DV336";


        if (!($this->check_signature($secret, $req_array['requestId'], $req_array['signature']))){
            $response_errors = $this->error_msg("0", "1", "wrong signature");

            if (!($this->check_time($req_array['time']))){
                $response_errors = $this->error_msg("0", "2", "request is expired");
            }
        } else {
            $response_errors = $this->error_msg("1", "0", "");
        }

        if($req_array['method'] != 'ping'){
            if($this->check_token($req_array['token'])){
                $response_errors = $this->error_msg("1", "0", "");

                switch($req_array['method']){
                    case "get_account_details":
                        $info = PersonalAccessToken::findToken($req_array['token'])->tokenable;
                        break;
                    case "refresh_token":
                    case "request_new_token":
                        if (config('sanctum.expiration') )
                        config(['sanctum.expiration' => self::TOKEN_EXPIRATION_TIME]);
                        break;
                }
            } else {
                $response_errors = $this->error_msg("0", "3", "invalid token");
            }
        }

        return response((
            $this->xml_response($req_array['method'], $req_array['token'], $response_errors, $info ?? null, $secret))
                ->asXML())
                ->header('Content-Type', 'application/xml');
    }

    function check_signature($secret, $requestId, $signature){
        return hash_hmac('sha256', $requestId, $secret) === $signature ? true : false;
    }

    function check_time($time){
        return time() - $time <= 60 ? true : false;
    }

    function check_token($sactumToken){
        return PersonalAccessToken::findToken($sactumToken) ? true : false;
    }

    function error_msg($success_code, $code, $text){
        $response['success'] = $success_code;
        $response['error_code'] = $code;
        $response['error_text'] = $text;
        return $response;
    }
}
