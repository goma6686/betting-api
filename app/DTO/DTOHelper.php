<?php
declare(strict_types=1);

namespace App\DTO;

use SimpleXMLElement;

class DTOHelper{
    public static function fromXml(SimpleXMLElement $xml){
        
        $method = (string)$xml->method;
        $token = (string)$xml->token;
        $request_id = (string)$xml->request_id;
        $time = (int)$xml->time;
        $signature = (string)$xml->signature;
        $amount = (int)$xml->params->children()->amount ?? null;
        $currency = (string)$xml->params->children()->currency ?? null;
        $betId = (int)$xml->params->children()->bet_id ?? null;
        $transactionId = (int)$xml->params->children()->transaction_id ?? null;
        $retrying = (int)$xml->params->children()->retrying ?? null;
        $player_id = (string)$xml->params->children()->player_id ?? (string)$xml->params;

        return new XmlRequest($method, $token, $request_id, $time, $signature, $amount, $currency, $betId, $transactionId, $retrying, $player_id);
    }

    public static function toXml(string $method, string $token, array $response, ?array $info, $responseId, $signature): string{

        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $method);
        $xmlResponse->addChild('token', $token);
        $xmlResponse->addChild('success', (string)$response['success']);
        $xmlResponse->addChild('error_code',(string)$response['error_code']);
        $xmlResponse->addChild('error_text', $response['error_text']);
        
        if($response['success'] !== '0' && $info !== null){
            $params = $xmlResponse->addChild('params');
            foreach ($info as $key => $value) {
                $params->addChild($key, (string)$value);
            }
        }

        $xmlResponse->addChild('response_id', $responseId);
        $xmlResponse->addChild('time', (string)time());
        $xmlResponse->addChild('signature', (string)$signature);

        return $xmlResponse->asXML();
    }
}