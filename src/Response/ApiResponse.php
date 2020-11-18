<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    protected $response;

    /**
     * @param mixed result
     * @param int $httpCode
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function success($result, $httpCode): JsonResponse
    {
        return new JsonResponse([
            'code' => 0,
            'message' => '',
            'result' => $result,
        ], $httpCode);
    }

    /**
     * @param int $contentCode
     * @param string $contentMsg
     * @param int $httpCode
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function fail(
        $contentCode,
        $contentMsg,
        $httpCode
    ): JsonResponse {
        return new JsonResponse([
            'code' => $contentCode,
            'message' => $contentMsg,
            'result' => null,
        ], $httpCode);
    }
}
