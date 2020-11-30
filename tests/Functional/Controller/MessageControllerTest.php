<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\CommentFixtures;
use App\DataFixtures\MessageFixtures;
use App\Tests\DatabaseTestCase;

class MessageControllerTest extends DatabaseTestCase
{
    public function testIndex()
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/api/board/message');

        //assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function provideStoreUrls()
    {
        return [
            //success
            [
                '/api/board/message',
                ['author' => 'paul', 'title' => 'aaaa', 'content' => 'bbbb'],
                200
            ],
            //wrong parameter
            [
                '/api/board/message',
                ['name' => '', 'content' => ''],
                400
            ],
        ];
    }

    /**
     * @dataProvider provideStoreUrls
     */
    public function testStore($url, $content, $responseCode): void
    {
        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('POST', $url, $content);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function provideShowUrls()
    {
        return [
            //success
            [
                '/api/board/message/1',
                200
            ],
            //message not found
            [
                '/api/board/message/999',
                404
            ],
            //wrong route
            [
                '/api/board/message/aaa',
                404
            ],
        ];
    }

    /**
     * @dataProvider provideShowUrls
     */
    public function testShow($url, $responseCode): void
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('GET', $url);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }


    public function provideUpdateUrls()
    {
        return [
            //success
            [
                '/api/board/message/1',
                ['content' => 'aaaa'],
                200
            ],
            //wrong parameter
            [
                '/api/board/message/1',
                ['content' => ''],
                400
            ],
            //message not found
            [
                '/api/board/message/999',
                [],
                404
            ],
            //wrong route
            [
                '/api/board/message/aaa',
                [],
                404
            ],
        ];
    }

    /**
     * @dataProvider provideUpdateUrls
     */
    public function testUpdate($url, $content, $responseCode): void
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('PUT', $url, $content);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function provideDeleteUrls()
    {
        return [
            //success
            [
                '/api/board/message/1',
                200
            ],
            //message not found
            [
                '/api/board/message/999',
                404
            ],
            //wrong route
            [
                '/api/board/message/aaa',
                404
            ],
        ];
    }

    /**
     * @dataProvider provideDeleteUrls
     */
    public function testDelete($url, $responseCode): void
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('DELETE', $url);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
