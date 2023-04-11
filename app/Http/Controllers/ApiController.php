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

        $responseId = $this->guidv4();

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
        $xmlResponse->addChild('response_id', $responseId); //UUID
        //$xmlResponse->addChild('time', time());
        $xmlResponse->addChild('time', (string) $xml->time);
        $xmlResponse->addChild('signature', hash_hmac('sha256', $responseId, $secret));

        return response($xmlResponse->asXML())->header('Content-Type', 'application/xml');
    }

    function guidv4($data = null) {
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

    public function ping($secret, $requestId, $signature){

        if (hash_hmac('sha256', $requestId, $secret) === $signature){
            return true;
        } else {
            return false;
        }
    }
}
