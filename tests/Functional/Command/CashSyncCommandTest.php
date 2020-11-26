<?php

namespace App\Tests\Functional\Command;

use App\Command\CashSyncCommand;
use App\DataFixtures\UserFixtures;
use App\Service\CashService;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CashSyncCommandTest extends DatabaseTestCase
{
    public function dataProvider()
    {
        return [
            [true, 'Update cash success.'],
            [false, 'Nothing update cash.'],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExecute($updateReslt, $msg)
    {
        //arrange
        $this->loadFixture(UserFixtures::class);

        /** @var MockObject&CashService */
        $cashServiceMock = $this->getMockBuilder(CashService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cashServiceMock
            ->method('updateCashList')
            ->willReturn($updateReslt);

        $kernel = static::createKernel();
        $application = new Application($kernel);
        $application->add(new CashSyncCommand($cashServiceMock));
        $command = $application->find('app:cash-sync');

        //act
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        //assert
        $this->assertContains($msg, $output);
    }
}
