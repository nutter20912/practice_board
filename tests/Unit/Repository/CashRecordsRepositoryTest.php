<?php

namespace App\Tests\Unit\Repository;

use App\DataFixtures\CashRecordFixtures;
use App\Entity\CashRecords;
use App\Tests\DatabaseTestCase;

class CashRecordsRepositoryTest extends DatabaseTestCase
{
    public function dataProvider()
    {
        return [
            [
                [
                    'user_id = :id' => 1,
                    'created_at > :start' => (new \DateTime())->format('Y-m-d') . ' 00:00:00',
                    'created_at < :end' => (new \DateTime())->format('Y-m-d') . ' 23:59:59',
                ],
                5,
                1
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $condition
     * @param int $pageLimit
     * @param int $currentPage
     */
    public function testGetRecordsByDate($condition, $pageLimit, $currentPage): void
    {
        //arrange
        $CashRecordFixtures = new CashRecordFixtures();
        $this->loadFixture($CashRecordFixtures);
        $user = $CashRecordFixtures->getUser();
        $condition['user_id = :id'] = $user->getId();

        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(CashRecords::class);

        //act
        $response = $repository
            ->setPageLimit($pageLimit)
            ->setCurrentPage($currentPage)
            ->getRecordsByDate($condition);

        //assert
        $this->assertIsArray($response);
        $this->assertCount(1, $response);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $condition
     * @param int $pageLimit
     * @param int $currentPage
     */
    public function testGetRecordsPages($condition, $pageLimit, $currentPage): void
    {
        //arrange
        $CashRecordFixtures = new CashRecordFixtures();
        $this->loadFixture($CashRecordFixtures);
        $user = $CashRecordFixtures->getUser();
        $condition['user_id = :id'] = $user->getId();

        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(CashRecords::class);

        //act
        $response = $repository
            ->setPageLimit($pageLimit)
            ->setCurrentPage($currentPage)
            ->getRecordsPages($condition);

        //assert
        $this->assertIsInt($response);
        $this->assertEquals(1, $response);
    }
}
