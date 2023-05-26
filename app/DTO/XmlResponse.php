<?php
namespace App\DTO;

use Illuminate\Support\Str;

use Laravel\Sanctum\PersonalAccessToken;

final class XmlResponse
{
    private function __construct(
        public readonly string $method,
        public readonly string $token,
        public readonly string $success,
        public readonly string $errorCode,
        public readonly string $errorText,
        public readonly ?array $params = null,
        public readonly string $responseId,
        public readonly int $time,
        public readonly string $signature
    ) {}

    public static function xml_response(
        string $method,
        string $token,
        array $response,
        ?array $info = null,
        string $secret
    ): self {

        $params = [];

        switch ($method){
            case 'get_account_details':
            case 'get_balance':
            case 'request_new_token':
                $params = $info;
                break;

            case 'transaction_bet_payin':
            case 'transaction_bet_payout':
                $params['balance_after'] = PersonalAccessToken::findToken($token)->tokenable['balance'] ?? null;
                $params['already_processed'] = $info['already_processed'] ?? null;
                break;
        }
            $responseId = Str::uuid()->toString();
            $signature = hash_hmac('sha256', $responseId, $secret);

        return new self(
            $method,
            $token,
            $response['success'],
            $response['error_code'],
            $response['error_text'],
            $params,
            $responseId,
            time(),
            $signature
        );
    }

    public function toXmlString(): string
    {
        $xmlResponse = new \SimpleXMLElement('<root/>');
        $xmlResponse->addChild('method', $this->method);
        $xmlResponse->addChild('token', $this->token);
        $xmlResponse->addChild('success', $this->success);
        $xmlResponse->addChild('error_code', $this->errorCode);
        $xmlResponse->addChild('error_text', $this->errorText);

        if($this->success !== '0' && $this->params !== null){
            $params = $xmlResponse->addChild('params');
            foreach ($this->params as $key => $value) {
                $params->addChild($key, $value);
            }
        }

        $xmlResponse->addChild('response_id', $this->responseId);
        $xmlResponse->addChild('time', $this->time);
        $xmlResponse->addChild('signature', $this->signature);

        return $xmlResponse->asXML();
    }
}