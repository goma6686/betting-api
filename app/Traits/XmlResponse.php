<?php
namespace App\Traits;

use Illuminate\Support\Str;

use Laravel\Sanctum\PersonalAccessToken;

trait XmlResponse
{
    protected function xml_response($method, $token, $response, $info, $secret){
        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', $response['success']);
        $xmlResponse->addChild('error_code', $response['error_code']);
        $xmlResponse->addChild('error_text', $response['error_text']);

        if($response['success'] !== '0'){
            $params = $xmlResponse->addChild('params');

            switch ($method){
                case 'get_account_details':
                    $params->addChild('user_id', $info['id']);
                    $params->addChild('username', $info['username']);
                    $params->addChild('currency', $info['currency']);
                    $params->addChild('info', $info['token']);
                    break;

                case 'get_balance':
                    $params->addChild('balance', (PersonalAccessToken::findToken($token))->tokenable['balance']);
                    break;

                case 'request_new_token':
                    $params->addChild('new_token', $token); //SUKURTI NAUJA??
                    break;

                case 'transaction_bet_payin':
                    $params->addChild('balance_after', (PersonalAccessToken::findToken($token))->tokenable['balance']);
                    $params->addChild('already_processed', $info['already_processed']);
                    break;
            }
        }
        $responseId = Str::uuid()->toString();
        $xmlResponse->addChild('response_id', $responseId); //UUID
        $xmlResponse->addChild('time', time());
        $xmlResponse->addChild('signature', hash_hmac('sha256', $responseId, $secret));

        return $xmlResponse;
    }
}