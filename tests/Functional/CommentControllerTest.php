<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\CommentFixtures;
use App\DataFixtures\MessageFixtures;
use App\Tests\DatabaseTestCase;

class CommentControllerTest extends DatabaseTestCase
{
    public function provideStoreUrls()
    {
        return [
            //success
            [
                '/api/board/message/1/comment',
                ['name' => 'paul', 'content' => 'aaaa'],
                200
            ],
            //wrong parameter
            [
                '/api/board/message/1/comment',
                ['name' => '', 'content' => ''],
                400
            ],
            //user not found
            [
                '/api/board/message/999/comment',
                [],
                404
            ],
            //wrong route
            [
                '/api/board/message/aaaa/comment',
                [],
                404
            ],
        ];
    }

    /**
     * @dataProvider provideStoreUrls
     */
    public function testStore($url, $content, $responseCode): void
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('POST', $url, $content);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }


    public function provideUpdateUrls()
    {
        return [
            //success
            [
                '/api/board/message/1/comment/1',
                ['content' => 'aaaa'],
                200
            ],
            //wrong parameter
            [
                '/api/board/message/1/comment/1',
                ['content' => ''],
                400
            ],
            //comment not found
            [
                '/api/board/message/1/comment/999',
                [],
                404
            ],
            //message not found
            [
                '/api/board/message/999/comment/1',
                [],
                404
            ],
            //wrong route
            [
                '/api/board/message/1/comment/aaa',
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
        $this->loadFixture(CommentFixtures::class);

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
                '/api/board/message/1/comment/1',
                200
            ],
            //comment not found
            [
                '/api/board/message/1/comment/999',
                404
            ],
            //message not found
            [
                '/api/board/message/999/comment/1',
                404
            ],
            //wrong route
            [
                '/api/board/message/1/comment/aaa',
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
        $this->loadFixture(CommentFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('DELETE', $url);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
