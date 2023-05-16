<?php
namespace App\DTO;

use Illuminate\Support\Str;

use Laravel\Sanctum\PersonalAccessToken;

final class XmlResponse
{
    public function __construct(
        public readonly string $method,
        public readonly string $token,
        public readonly string $success,
        public readonly string $errorCode,
        public readonly string $errorText,
        public ?array $params = null,
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
        $params = null;

        if($response['success'] !== '0'){
            $params = [];

            switch ($method){
                case 'get_account_details':
                    $params['user_id'] = $info['id'];
                    $params['username'] = $info['username'];
                    $params['currency'] = $info['currency'];
                    $params['info'] = $info['token'];
                    break;

                case 'get_balance':
                    $params['balance'] = PersonalAccessToken::findToken($token)->tokenable['balance'];
                    break;

                case 'request_new_token':
                    $params['new_token'] = $token;
                    break;

                case 'transaction_bet_payin':
                    case 'transaction_bet_payout':
                    $params['balance_after'] = PersonalAccessToken::findToken($token)->tokenable['balance'];
                    $params['already_processed'] = $info['already_processed'];
                    break;
            }
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

        if ($this->params !== null) {
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