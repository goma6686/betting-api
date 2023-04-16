<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiController extends Controller
{
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

        $responseId = $this->generate_UUID();

        $response_errors = $this->check_signature($secret, $requestId, $signature);
        if ($this->check_signature($secret, $requestId, $signature)){
            $response_errors = $this->error_msg("1", "0", "");
            if ($this->check_time($time)){
                $response_errors = $this->error_msg("1", "0", "");
            } else {
                $response_errors = $this->error_msg("0", "2", "request is expired");
            }
        } else {
            $response_errors = $this->error_msg("0", "1", "wrong signature");
        }

        switch($method){
            case "get_account_details":
                if($this->check_token($token)){
                    $response_errors = $this->error_msg("1", "0", "");
                    $info = PersonalAccessToken::findToken($token)->tokenable;
                } else {
                    $response_errors = $this->error_msg("0", "3", "invalid token");
                }
                break;
            case "refresh_token":
                if($this->check_token($token)){
                    $response_errors = $this->error_msg("1", "0", "");
                } else {
                    $response_errors = $this->error_msg("0", "3", "invalid token");
                }
        }

        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', $response_errors[0]);
        $xmlResponse->addChild('error_code', $response_errors[1]);
        $xmlResponse->addChild('error_text', $response_errors[2]);

        $params = $xmlResponse->addChild('params');
        $params->addChild('user_id', $info['id']);
        $params->addChild('username', $info['username']);
        $params->addChild('currency', 'EUR');
        $params->addChild('info', $token);

        $xmlResponse->addChild('response_id', $responseId); //UUID
        $xmlResponse->addChild('time', time());
        $xmlResponse->addChild('signature', hash_hmac('sha256', $responseId, $secret));

        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');
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

    function generate_UUID($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
