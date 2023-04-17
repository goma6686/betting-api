<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Traits\XmlResponse; 

class ApiController extends Controller
{
    use XmlResponse;

    public function method(Request $request){
        $xmlData = $request->getContent();
        
        $xml = simplexml_load_string($xmlData);

        $method = (string) $xml->method;

        $token = (string) $xml->token;
        $requestId = (string) $xml->request_id;
        $signature = (string) $xml->signature;
        $time = (string) $xml->time;
        $params = (string) $xml->params;

        $secret = "CCHWS-ZIFJV-HEAOB-DV336";


        if (!($this->check_signature($secret, $requestId, $signature))){
            $response_errors = $this->error_msg("0", "1", "wrong signature");
            if (!($this->check_time($time))){
                $response_errors = $this->error_msg("0", "2", "request is expired");
            }
        } else {
            $response_errors = $this->error_msg("1", "0", "");
        }

        if($method != 'ping'){
            if($this->check_token($token)){
                $response_errors = $this->error_msg("1", "0", "");

                switch($method){
                    case "get_account_details":
                        $info = PersonalAccessToken::findToken($token)->tokenable;
                        break;
                }
            } else {
                $response_errors = $this->error_msg("0", "3", "invalid token");
            }
        }
        if ($method === "ping" || $method === "refresh_token") $info = null;

        return response(($this->xml_response($method, $token, $response_errors, $info, $params, $secret))->asXML())->header('Content-Type', 'application/xml');
    }

    function check_signature($secret, $requestId, $signature){
        return hash_hmac('sha256', $requestId, $secret) === $signature ? true : false;
    }

    function check_time($time){
        //return time() - $time <= 60 ? true : false;
        return true;
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
