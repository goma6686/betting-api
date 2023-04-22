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
            $params['amount'] = (string) $xml->params->amount;
            $params['currency'] = (string) $xml->params->currency;
            $params['bet_id'] = (string) $xml->params->bet_id;
            $params['transaction_id'] = (string) $xml->transaction_id;
            $params['retrying'] = (string) $xml->params->retrying;
        }

        return array($arr, $params ?? null);
    }
}