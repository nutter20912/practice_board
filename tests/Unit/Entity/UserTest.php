<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser(): void
    {
        $user = new User();

        $this->assertNull($user->getId());

        $user->setAccount('paul');
        $this->assertEquals('paul', $user->getAccount());

        $user->setCash(10);
        $this->assertEquals(10, $user->getCash());

        $date = new \DateTime();
        $user->setCreatedAt($date);
        $this->assertEquals($date, $user->getCreatedAt());

        $user->setUpdatedAt($date);
        $this->assertEquals($date, $user->getUpdatedAt());

        $this->assertInstanceOf(Collection::class, $user->getCashRecords());
    }
}
