<?php

namespace App\Tests\Functional\Command;

use App\DataFixtures\UserFixtures;
use App\Tests\DatabaseTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CashConcurrencyCommandTest extends DatabaseTestCase
{
    public function testExecuteSuccess()
    {
        //arrange
        $this->loadFixture(UserFixtures::class);
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:cash-concurrency');

        //act
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'action' => 'add',
            'account' => 'paul',
            '-N' => 1,
            '-C' => 1,
        ]);
        $output = $commandTester->getDisplay();

        //assert
        $this->assertContains('Cash concurrency test complete.', $output);
    }

    public function testExecuteNotFoundUser()
    {
        //arrange
        $this->loadFixture(UserFixtures::class);
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:cash-concurrency');

        //act
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'action' => 'add',
            'account' => 'QQQ',
        ]);
        $output = $commandTester->getDisplay();

        //assert
        $this->assertContains(sprintf('Not found user: %s', 'QQQ'), $output);
    }

    /**
     * @expectedException Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testExecuteProccessFail()
    {
        //arrange
        $this->loadFixture(UserFixtures::class);
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:cash-concurrency');

        //act
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'action' => 'add',
            'account' => 'paul',
            '-N' => 'error input',
        ]);
    }
}
