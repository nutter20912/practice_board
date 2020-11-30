<?php

namespace App\Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\DatabaseTestCase;

class UserRepositoryTest extends DatabaseTestCase
{
    public function testConstruct(): void
    {
        //arrange
        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(User::class);

        //assert
        $this->assertInstanceOf(UserRepository::class, $repository);
    }
}
