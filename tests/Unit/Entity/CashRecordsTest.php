<?php

namespace App\Tests\Entity;

use App\Entity\CashRecords;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CashRecordsTest extends TestCase
{
    public function testCashRecords(): void
    {
        $CashRecords = new CashRecords();

        $this->assertNull($CashRecords->getId());

        $CashRecords->setOperator('paul');
        $this->assertEquals('paul', $CashRecords->getOperator());

        $CashRecords->setDiff(10);
        $this->assertEquals(10, $CashRecords->getDiff());

        $CashRecords->setCurrent(10);
        $this->assertEquals(10, $CashRecords->getCurrent());

        $CashRecords->setUserId(10);
        $this->assertEquals(10, $CashRecords->getUserId());

        $CashRecords->setIp('127.0.0.1');
        $this->assertEquals('127.0.0.1', $CashRecords->getIp());

        $date = new \DateTime();
        $CashRecords->setCreatedAt($date);
        $this->assertEquals($date, $CashRecords->getCreatedAt());

        /** @var MockObject&User */
        $user = $this->getMockBuilder(User::class)->getMock();
        $CashRecords->setUser($user);
        $this->assertEquals($user, $CashRecords->getUser());
    }
}
