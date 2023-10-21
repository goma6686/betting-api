<?php
declare(strict_types=1);
namespace App\DTO;

final class XmlResponse
{
    private function __construct(
        public readonly string $method,
        public readonly string $token,
        public readonly int $success,
        public readonly int $errorCode,
        public readonly string $errorText,
        public readonly ?array $params = null,
        public readonly int $responseId,
        public readonly int $time,
        public readonly string $signature
    ) {}
}