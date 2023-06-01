<?php

namespace App\Enums;

class ResponseStatus
{
    const WRONG_SIGNATURE = 1;
    const REQUEST_EXPIRED = 2;
    const INVALID_TOKEN = 3;
    const NO_PAYIN = 700;
    const INSUFFICIENT_BALANCE = 703;

    public static $statusText = [
        self::WRONG_SIGNATURE => 'wrong signature',
        self::REQUEST_EXPIRED => 'request is expired',
        self::INVALID_TOKEN => 'invalid token',
        self::INSUFFICIENT_BALANCE => 'insufficient balance',
        self::NO_PAYIN => 'there is no PAYIN with provided bet_id',
    ];
}