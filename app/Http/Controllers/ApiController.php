<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Traits\XmlResponse;
use App\Traits\XmlRequest; 

class ApiController extends Controller
{
    use XmlResponse, XmlRequest;

    public function method(Request $request){
        $arr = $this->xml_request($request->getContent());

        $secret = "CCHWS-ZIFJV-HEAOB-DV336";


        if (!($this->check_signature($secret, $arr['requestId'], $arr['signature']))){
            $response_errors = $this->error_msg("0", "1", "wrong signature");
            if (!($this->check_time($arr['time']))){
                $response_errors = $this->error_msg("0", "2", "request is expired");
            }
        } else {
            $response_errors = $this->error_msg("1", "0", "");
        }

        if($arr['method'] != 'ping'){
            if($this->check_token($arr['token'])){
                $response_errors = $this->error_msg("1", "0", "");

                switch($arr['method']){
                    case "get_account_details":
                        $info = PersonalAccessToken::findToken($arr['token'])->tokenable;
                        break;
                }
            } else {
                $response_errors = $this->error_msg("0", "3", "invalid token");
            }
        }

        return response((
            $this->xml_response($arr['method'], $arr['token'], $response_errors, $info ?? null, $arr['params'], $secret))
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
        $success = $success_code;
        $error_code = $code;
        $error_text = $text;
        return array($success, $error_code, $error_text);
    }
}
