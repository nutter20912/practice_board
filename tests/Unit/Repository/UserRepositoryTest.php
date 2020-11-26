<?php

namespace App\Tests\Unit\Repository;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\DatabaseTestCase;

class UserRepositoryTest extends DatabaseTestCase
{
    public function testUpdateCash()
    {
        //arrange
        $this->loadFixture(UserFixtures::class);

        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(User::class);

        //act
        $response = $repository->updateCash(1, 100);

        //assert
        $this->assertEquals(1, $response);
    }
}
