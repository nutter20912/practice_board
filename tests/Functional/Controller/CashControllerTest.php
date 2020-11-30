<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\CashRecordFixtures;
use App\DataFixtures\UserFixtures;
use App\Tests\DatabaseTestCase;

class CashControllerTest extends DatabaseTestCase
{
    public function provideShowUrls()
    {
        return [
            ['/api/cash/1', 200],   //success
            ['/api/cash/999', 404], //user not found
            ['/api/cash/aaa', 404], //route error
        ];
    }

    /**
     * @dataProvider provideShowUrls
     */
    public function testShow($url, $responseCode): void
    {
        //arrange
        $this->loadFixture(UserFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $url);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function cashActionProvider()
    {
        return [
            //add
            ['/api/cash/1/add', ["cash" => 1], 200],    //success
            ['/api/cash/1/add', [], 400],               //param error
            ['/api/cash/1/add', ["cash" => 0], 400],    //zero cash
            ['/api/cash/1/add', ["cash" => 'a'], 400],  //cash error
            ['/api/cash/999/add', ["cash" => 1], 404],  //user not found
            //sub
            ['/api/cash/1/sub', ["cash" => 1], 200],        //success
            ['/api/cash/1/sub', ["cash" => 'a'], 400],      //cash error
            ['/api/cash/1/sub', ["cash" => 9999999], 400],  //over cash

        ];
    }

    /**
     * @dataProvider cashActionProvider
     */
    public function testAddOrSub($url, $content, $responseCode): void
    {
        //arrange
        $this->loadFixture(UserFixtures::class);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('PUT', $url, $content);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function recordsProvider()
    {
        return [
            //success
            [
                '/api/cash/1/records', [
                    "start" => (new \DateTime())->format('Y-m-d') . ' 00:00:00',
                    "end" => (new \DateTime())->format('Y-m-d') . ' 23:59:59',
                ], 200
            ],
            //user not found
            [
                '/api/cash/999/records', [
                    "start" => (new \DateTime())->format('Y-m-d') . ' 00:00:00',
                    "end" => (new \DateTime())->format('Y-m-d') . ' 23:59:59',
                ], 404
            ],
            //route error
            [
                '/api/cash/aaa/records', [
                    "start" => (new \DateTime())->format('Y-m-d') . ' 00:00:00',
                    "end" => (new \DateTime())->format('Y-m-d') . ' 23:59:59',
                ], 404
            ]
        ];
    }

    /**
     * @dataProvider recordsProvider
     */
    public function testRecords($url, $content, $responseCode): void
    {
        //arrange
        $CashRecordFixtures = new CashRecordFixtures();
        $this->loadFixture($CashRecordFixtures);

        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->xmlHttpRequest('GET', $url, $content);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
