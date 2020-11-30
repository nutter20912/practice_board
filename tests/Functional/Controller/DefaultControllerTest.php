<?php

namespace App\Tests\Functional\Controller;

use App\Tests\DatabaseTestCase;

class DefaultControllerTest extends DatabaseTestCase
{
    public function provideShowUrls()
    {
        return [
            ['/board', 200],
            ['/user', 200],
        ];
    }

    /**
     * @dataProvider provideShowUrls
     */
    public function testShowIndex($url, $responseCode): void
    {
        //act
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $url);

        //assert
        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
    }
}
