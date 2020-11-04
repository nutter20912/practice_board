<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    protected $response;

    public function __construct()
    {
    }

    public static function success($result, $httpCode): JsonResponse
    {
        return new JsonResponse([
            'code' => 0,
            'message' => '',
            'result' => $result,
        ], $httpCode);
    }

    public static function fail($respone, $httpCode): JsonResponse
    {
        return new JsonResponse([
            'code' => $respone['Code'],
            'message' => $respone['Message'],
            'result' => null,
        ], $httpCode);
    }
}
