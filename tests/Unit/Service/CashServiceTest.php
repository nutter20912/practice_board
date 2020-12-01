<?php

namespace App\Tests\Unit\Service;

use App\DataFixtures\UserFixtures;
use App\Entity\CashRecords;
use App\Entity\User;
use App\Service\CashService;
use App\Tests\DatabaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;

class CashServiceTest extends DatabaseTestCase
{
    private function getUserMock($id, $account, $cash): User
    {
        /** @var MockObject&User */
        $userMcok = $this
            ->getMockBuilder(User::class)
            ->getMock();
        $userMcok
            ->method('getId')
            ->willReturn($id);
        $userMcok
            ->method('getAccount')
            ->willReturn($account);
        $userMcok
            ->method('getCash')
            ->willReturn($cash);

        return $userMcok;
    }

    public function testGetCashRedisLiving()
    {
        //arrange
        $redisCash = 100;
        $databaseCash = 200;

        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('setNx')
            ->willReturn(false);
        $redisMcok
            ->method('get')
            ->willReturn($redisCash);

        /** @var MockObject&EntityManagerInterface */
        $entityManagerMcok = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        /** @var MockObject&User */
        $userMcok = $this->getUserMock(1, 'paul', $databaseCash);

        //act
        $service = new CashService($entityManagerMcok, $redisMcok);
        $response = $service->getCash($userMcok);

        //assert
        $this->assertIsFloat($response);
        $this->assertEquals(0, bccomp($redisCash, $response));
    }

    public function testGetCashRedisExpired()
    {
        //arrange
        $databaseCash = 200;

        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('setNx')
            ->willReturn(true);
        $redisMcok
            ->method('expire')
            ->willReturn(true);

        /** @var MockObject&EntityManagerInterface */
        $entityManagerMcok = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        /** @var MockObject&User */
        $userMcok = $this->getUserMock(1, 'paul', $databaseCash);

        //act
        $service = new CashService($entityManagerMcok, $redisMcok);
        $response = $service->getCash($userMcok);

        //assert
        $this->assertIsFloat($response);
        $this->assertEquals(0, bccomp($databaseCash, $response));
    }

    public function changeCashProvider()
    {
        return [
            [100, 50],
            [100.0, -99.8],
        ];
    }

    /**
     * @dataProvider changeCashProvider
     */
    public function testChangeCashSuccess($cash, $diff)
    {
        //arrange
        $newCash = bcadd($cash, $diff, 3);

        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('setNx')
            ->willReturn(false);
        $redisMcok
            ->method('get')
            ->willReturn($cash);
        $redisMcok
            ->method('set')
            ->willReturn(true);

        /** @var MockObject&EntityManagerInterface */
        $entityManagerMcok = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        /** @var MockObject&User */
        $userMcok = $this->getUserMock(1, 'paul', $cash);

        //act
        $service = new CashService($entityManagerMcok, $redisMcok);
        $response = $service->changeCash($userMcok, $diff);

        //assert
        $this->assertIsFloat($response);
        $this->assertEquals(0, bccomp($newCash, $response));
    }

    public function changeFailProvider()
    {
        return [
            [-10000], //over cash
            [0],     //zero cash
        ];
    }

    /**
     * @dataProvider changeFailProvider
     *
     * @expectedException App\Exceptions\ApiValidationException
     */
    public function testChangeCashFail($diff)
    {
        //arrange
        $cash = 100;

        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('get')
            ->willReturn($cash);
        $redisMcok
            ->method('setNx')
            ->willReturn(false);

        /** @var MockObject&EntityManagerInterface */
        $entityManagerMcok = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        /** @var MockObject&User */
        $userMcok = $this->getUserMock(1, 'paul', $cash);

        //act
        $service = new CashService($entityManagerMcok, $redisMcok);
        $response = $service->changeCash($userMcok, $diff);
    }

    public function testAddCashRecord()
    {
        //arrange
        $cash = 100;
        $diff = 50;
        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('select')
            ->willReturn(true);
        $redisMcok
            ->method('rPush')
            ->willReturn(true);

        /** @var MockObject&EntityManagerInterface */
        $entityManagerMcok = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        /** @var MockObject&Request */
        $requestMcok = $this
            ->getMockBuilder(Request::class)
            ->getMock();
        $requestMcok
            ->method('getClientIp')
            ->willReturn('127.0.0.1');

        /** @var MockObject&User */
        $userMcok = $this->getUserMock(1, 'paul', $cash);

        //act
        $service = new CashService($entityManagerMcok, $redisMcok);
        $response = $service->addCashRecord($requestMcok, $userMcok, $cash, $diff);

        //assert
        $this->assertInstanceOf(CashRecords::class, $response);
    }

    public function updateProvider()
    {
        return [
            [1, true],  // success
            [0, false], // empty list
        ];
    }

    /**
     * @dataProvider updateProvider
     */
    public function testUpdateCashListSuccess($count, $result)
    {
        //arrange
        $res = '{"userId":1,"recordId":1,"diff":10}';
        $this->loadFixture(UserFixtures::class);

        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('select')
            ->willReturn(true);
        $redisMcok
            ->method('lLen')
            ->willReturn($count);
        $redisMcok
            ->method('lPop')
            ->will($this->onConsecutiveCalls($res, false));

        $entityManager = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        //act
        $service = new CashService($entityManager, $redisMcok);
        $response = $service->updateCashList();

        //assert
        $this->assertEquals($result, $response);
    }

    /**
     * @expectedException \Exception
     */
    public function testUpdateCashListWithErrorUser()
    {
        //arrange
        $res = '{"userId":999,"recordId":1,"diff":10}';
        $this->loadFixture(UserFixtures::class);

        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();
        $redisMcok
            ->method('select')
            ->willReturn(true);
        $redisMcok
            ->method('lLen')
            ->willReturn(1);
        $redisMcok
            ->method('lPop')
            ->will($this->onConsecutiveCalls($res, false));

        $entityManager = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        //act
        $service = new CashService($entityManager, $redisMcok);
        $response = $service->updateCashList();
    }

    public function FormatProvider()
    {
        return [
            [1, 1],
            //3位後捨去
            [1.0014, 1.001],
            [1.0019, 1.001],
        ];
    }

    /**
     * @dataProvider FormatProvider
     */
    public function testCashFormat($cash, $expect)
    {
        /** @var MockObject&\Redis */
        $redisMcok = $this
            ->getMockBuilder(\Redis::class)
            ->getMock();

        $entityManager = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        //act
        $service = new CashService($entityManager, $redisMcok);
        $response = $service->cashFormat($cash);

        //assert
        $this->assertEquals(0, bccomp($expect, $response));
    }
}
