<?php
namespace App\Traits;

trait XmlRequest
{
    protected function xml_request($xmlData){
        $xml = simplexml_load_string($xmlData);

        $arr['method'] = (string) $xml->method;

        $arr['token'] = (string) $xml->token;
        $arr['requestId'] = (string) $xml->request_id;
        $arr['signature'] = (string) $xml->signature;
        $arr['time'] = (string) $xml->time;
        $arr['params'] = (string) $xml->params;

        return $arr;
    }
}