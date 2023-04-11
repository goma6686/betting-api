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
        $signature = (string) $xml->signature;
        $params = (string) $xml->params;

        $secret = "CCHWS-ZIFJV-HEAOB-DV336";

        $success = 1;
        $error_code = 0;
        $error_text = '';

        switch ($method) {
            case "ping":
              if(!($this->ping($secret, $requestId, $signature))){
                $success = '0';
                $error_code = '1';
                $error_text = 'wrong_signature';
              };
              break;
        }
        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', $success);
        $xmlResponse->addChild('error_code', $error_code);
        $xmlResponse->addChild('error_text', $error_text);
        $xmlResponse->addChild('params', $params);
        $xmlResponse->addChild('response_id', hash_hmac('sha256', $requestId.$secret, $secret));
        $xmlResponse->addChild('time', time());
        $xmlResponse->addChild('signature', hash_hmac('sha256', hash_hmac('sha256', $requestId, $secret), $secret));

        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');
    }

    public function ping($secret, $requestId, $signature){

        if (hash_hmac('sha256', $requestId, $secret) === $signature){
            return true;
        } else {
            return false;
        }
    }
}
