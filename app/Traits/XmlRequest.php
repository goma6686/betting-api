<?php
namespace App\Traits;

trait XmlRequest
{
    protected function xml_request($xmlData){
        $xml = simplexml_load_string($xmlData);

        $arr['method'] = (string) $xml->method;

        $arr['token'] = (string) $xml->token;
        $arr['requestId'] = (string) $xml->request_id;
        $arr['time'] = (string) $xml->time;
        $arr['signature'] = (string) $xml->signature;
        $arr['params'] = (string) $xml->params;
        if(null !== ($xml->params->children())){
            $arr['amount'] = (string) $xml->params->children()->amount;
            $arr['currency'] = (string) $xml->params->children()->currency;
            $arr['bet_id'] = (string) $xml->params->children()->bet_id;
            $arr['transaction_id'] = (string) $xml->children()->transaction_id;
            $arr['retrying'] = (string) $xml->params->children()->retrying;
        }
        
        return $arr;
    }
}