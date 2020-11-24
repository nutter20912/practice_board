<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MessageFixtures extends Fixture
{
    const USER_REFERENCE = 'message';

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

        $this->addReference(self::USER_REFERENCE, $message);
    }
}
