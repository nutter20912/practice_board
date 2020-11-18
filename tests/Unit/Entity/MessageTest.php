<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testMessage(): void
    {
        $message = new Message();

        $this->assertNull($message->getId());

        $message->setAuthor('paul');
        $this->assertEquals('paul', $message->getAuthor());

        $message->setTitle('paul');
        $this->assertEquals('paul', $message->getTitle());

        $message->setContent('aaaa');
        $this->assertEquals('aaaa', $message->getContent());

        $date = new \DateTime();
        $message->setCreatedAt($date);
        $this->assertEquals($date, $message->getCreatedAt());

        $message->setUpdatedAt($date);
        $this->assertEquals($date, $message->getUpdatedAt());

        $this->assertInstanceOf(Collection::class, $message->getComment());
    }
}
