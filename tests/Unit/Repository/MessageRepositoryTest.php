<?php

namespace App\Tests\Unit\Repository;

use App\DataFixtures\MessageFixtures;
use App\Entity\Message;
use App\Tests\DatabaseTestCase;

class MessageRepositoryTest extends DatabaseTestCase
{
    public function dataProvider()
    {
        return [
            [5, 1]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetMessagePaginator($pageLimit, $currentPage): void
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);
        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Message::class);

        //act
        $response = $repository
            ->setPageLimit($pageLimit)
            ->setCurrentPage($currentPage)
            ->getMessagePaginator();

        //assert
        $this->assertIsArray($response);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetMessagePages($pageLimit, $currentPage): void
    {
        //arrange
        $this->loadFixture(MessageFixtures::class);
        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Message::class);

        //act
        $response = $repository
            ->setPageLimit($pageLimit)
            ->setCurrentPage($currentPage)
            ->getMessagePages();

        //assert
        $this->assertIsInt($response);
    }
}
