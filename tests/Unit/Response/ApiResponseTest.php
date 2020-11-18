<?php

namespace App\Tests\Unit\Response;

use App\Response\ApiResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseTest extends TestCase
{
    public function successProvider()
    {
        return [
            [
                'success msg', 201
            ],
            [
                ['aa' => 'bb'], 200
            ],

        ];
    }
    /**
     * @dataProvider successProvider
     */
    public function testSuccessFormat($msg, $httpcode): void
    {
        //act
        $response = ApiResponse::success($msg, $httpcode);
        $content = $response->getContent();

        //assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($httpcode, $response->getStatusCode());

        $data = json_decode($content, true);
        //固定回應格式
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('result', $data);
        //固定回應內容
        $this->assertEquals(0, $data['code']);
        $this->assertEquals('', $data['message']);
        $this->assertEquals($msg, $data['result']);
    }


    public function failProvider()
    {
        return [
            [901, 'bad parameter', 400],
            [902, 'error cash', 400],
        ];
    }
    /**
     * @dataProvider failProvider
     */
    public function testFailFormat($contentCode, $contentMsg, $httpcode): void
    {
        //act
        $response = ApiResponse::fail($contentCode, $contentMsg, $httpcode);
        $content = $response->getContent();

        //assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($httpcode, $response->getStatusCode());

        $data = json_decode($content, true);
        //固定回應格式
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('result', $data);
        //固定回應內容
        $this->assertEquals($contentCode, $data['code']);
        $this->assertEquals($contentMsg, $data['message']);
        $this->assertNull($data['result']);
    }
}
