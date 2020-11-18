<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MessageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $message = new Message();
        $message->setAuthor('paul');
        $message->setTitle('hello');
        $message->setContent('world');
        $message->setCreatedAt(new \DateTime());
        $message->setUpdatedAt(new \DateTime());
        $manager->persist($message);
        $manager->flush();
    }
}
