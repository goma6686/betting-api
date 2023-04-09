<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function method(Request $request){
        $xmlData = $request->getContent();
        
        $xml = simplexml_load_string($xmlData);

        $method = (string) $xml->method;

        $token = (string) $xml->token;
        $requestId = (string) $xml->request_id;
        $time = (string) $xml->time;
        $signature = (string) $xml->signature;
        $params = (string) $xml->params;

        $secret = "CCHWS-ZIFJV-HEAOB-DV336";

        $success = 1;
        $error_code = 0;
        $error_text = '';

        $calc_string = 'test-string';

        switch ($method) {
            case "ping":
              $this->ping($method, $secret, $requestId, $time, $signature, $success, $error_code, $error_text, $calc_string);
              break;
        }
        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', $success);
        $xmlResponse->addChild('error_code', $error_code);
        $xmlResponse->addChild('error_text', $error_text);
        $xmlResponse->addChild('params', $params);
        $xmlResponse->addChild('response_id', $requestId);
        $xmlResponse->addChild('time', time());
        $xmlResponse->addChild('signature', $signature);

        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');
    }

    public function ping($method, $secret, $requestId, $signature, $success, $error_code, $error_text, $calc_string){

        if (hash_hmac('sha256', $requestId, $secret) === $signature){
            $requestId = hash_hmac('md5', $calc_string , $secret);
            $signature = hash_hmac('sha256', $requestId, $secret);
        } else {
            $success = '0';
            $error_code = '1';
            $error_text = 'wrong_signature';
            $requestId = hash_hmac('md5', $calc_string , $secret);
            $signature = hash_hmac('sha256', $requestId, $secret);
        }
        return array($method, $secret, $requestId, $signature, $success, $error_code, $error_text);
    }

    private function signature($requestId, $secret){
        return hash_hmac('sha256', $requestId, $secret);
    }
}
