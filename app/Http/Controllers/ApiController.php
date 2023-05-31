<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\DTOHelper;
use Illuminate\Http\Request;
use App\Enums\ResponseStatus;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\ApiService;
use Carbon\Carbon;

class ApiController extends UserController
{
    private ApiService $apiService;

    private const SECRET = "CCHWS-ZIFJV-HEAOB-DV336";

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request){
        $requestDTO = DTOHelper::from_xml(simplexml_load_string($request->getContent()));

        $data = $this->apiService->index(self::SECRET, $requestDTO);
        
        return response(
            DTOHelper::to_xml($requestDTO->method, $requestDTO->token, $data['response_errors'], $data['params'] ?? null, $data['responseId'], time(), $data['signature'])
            )->header('Content-Type', 'application/xml');
    }

    function check_signature(string $secret, string $requestId, string $signature): bool{
        return hash_hmac('sha256', $requestId, $secret) === $signature;
    }

    function check_time(int $time): bool{
        //return time() - $time <= 60;
        return true;
    }
}
