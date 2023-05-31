<?php

namespace App\Http\Traits;

trait ResponseTrait {

    private function generateErrorResponse(int $success_code, int $code, string $text)
    {
        $response['success'] = $success_code;
        $response['error_code'] = $code;
        $response['error_text'] = $text;
        return $response;
    }

    private function generateSuccessResponse()
    {
        return $this->generateErrorResponse(1, 0, '');
    }
}