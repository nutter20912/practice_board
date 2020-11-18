<?php

namespace App\DataFixtures;

use App\Entity\CashRecords;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CashRecordFixtures extends Fixture implements DependentFixtureInterface
{
    protected $user;

    public function load(ObjectManager $manager)
    {
        $user = $this->getReference(UserFixtures::USER_REFERENCE);
        $cashRecord = new CashRecords();
        $cashRecord->setOperator($user->getAccount());
        $cashRecord->setCurrent(0);
        $cashRecord->setDiff(100);
        $cashRecord->setIp('127.0.0.1');
        $cashRecord->setUser($user);
        $manager->persist($cashRecord);
        $manager->flush();
        $this->setUser($user);
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
}
