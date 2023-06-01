<?php
declare(strict_types=1);
namespace App\DTO;

final class XmlRequest
{
    public function __construct(
        public readonly string $method,
        public readonly string $token,
        public readonly string $requestId,
        public readonly int $time,
        public readonly string $signature,
        public readonly ?int $amount = null,
        public readonly ?string $currency = null,
        public readonly ?int $betId = null,
        public readonly ?int $transactionId = null,
        public readonly ?int $retrying = null,
        public readonly ?string $player_id = null
    ) {}
}