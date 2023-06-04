<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\DTOHelper;
use Illuminate\Http\Request;
use App\Services\ApiService;

class ApiController extends UserController
{
    private ApiService $apiService;

    private const SECRET = "CCHWS-ZIFJV-HEAOB-DV336";

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request){
        $requestDTO = DTOHelper::fromXml(simplexml_load_string($request->getContent()));
        
        $data = $this->apiService->index(self::SECRET, $requestDTO);
        
        return response(
            DTOHelper::toXml($requestDTO->method, $requestDTO->token, $data['response_errors'], $data['params'] ?? null, $data['responseId'], $data['signature'])
            )->header('Content-Type', 'application/xml');
    }
}
