<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\UserFixtures;
use App\Tests\DatabaseTestCase;

class UserControllerTest extends DatabaseTestCase
{
    public function provideShowUrls()
    {
        return [
            ['/api/user/paul', 200], //success
            ['/api/user/leo', 404], //user not found
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
}
