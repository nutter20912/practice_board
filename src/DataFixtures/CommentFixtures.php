<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $message = $this->getReference(MessageFixtures::USER_REFERENCE);
        $comment = new Comment();
        $comment->setName('paul');
        $comment->setContent('content');
        $comment->setMessage($message);
        $comment->setCreatedAt(new \DateTime("now"));
        $comment->setUpdatedAt(new \DateTime("now"));
        $manager->persist($comment);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [MessageFixtures::class];
    }
}
