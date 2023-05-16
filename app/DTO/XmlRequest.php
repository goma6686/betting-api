<?php
namespace App\DTO;

final class XmlRequest
{
    public function __construct(
        public readonly string $method,
        public readonly string $token,
        public readonly string $requestId,
        public readonly string $time,
        public readonly string $signature,
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $betId = null,
        public readonly ?string $transactionId = null,
        public readonly ?string $retrying = null,
        public readonly ?string $params = null
    ) {}
    public static function xml_request(string $xmlData): self
    {
        $xml = simplexml_load_string($xmlData);

        $amount = $xml->params->children()->amount ?? null;
        $currency = $xml->params->children()->currency ?? null;
        $betId = $xml->params->children()->bet_id ?? null;
        $transactionId = $xml->params->children()->transaction_id ?? null;
        $retrying = $xml->params->children()->retrying ?? null;
        $params = $xml->params->children() ?? (string)$xml->params;

        return new self(
            (string)$xml->method,
            (string)$xml->token,
            (string)$xml->request_id,
            (string)$xml->time,
            (string)$xml->signature,
            $amount,
            $currency,
            $betId,
            $transactionId,
            $retrying,
            $params
        );
    }
}