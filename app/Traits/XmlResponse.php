<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait XmlResponse
{
    protected function xml_response($method, $token, $response_errors, $info, $params, $secret){
        $responseId = Str::uuid()->toString();

        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', $response_errors[0]);
        $xmlResponse->addChild('error_code', $response_errors[1]);
        $xmlResponse->addChild('error_text', $response_errors[2]);
        
        if($method === 'ping' || $method === 'refresh_token'){
            $xmlResponse->addChild('params', $params);
        } else {
            $params = $xmlResponse->addChild('params');

            if ($method === 'get_account_details'){
                $params->addChild('user_id', $info['id']);
                $params->addChild('username', $info['username']);
                $params->addChild('currency', 'EUR');
                $params->addChild('info', $token);

            } else if ($method === 'request_new_token'){
                $params->addChild('new_token', 'NEW TOKEN HERE');

            } else if ($method === 'get_balance') {
                $params->addChild('balance', 'PLAYER BALANCE');

            }
        }

        $xmlResponse->addChild('response_id', $responseId); //UUID
        $xmlResponse->addChild('time', time());
        $xmlResponse->addChild('signature', hash_hmac('sha256', $responseId, $secret));

        return $xmlResponse;
    }
}