<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Message;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testcomment(): void
    {
        $comment = new Comment();

        $this->assertNull($comment->getId());

        $comment->setMessageId(10);
        $this->assertEquals(10, $comment->getMessageId());

        $comment->setName('paul');
        $this->assertEquals('paul', $comment->getName());

        $date = new \DateTime();
        $comment->setCreatedAt($date);
        $this->assertEquals($date, $comment->getCreatedAt());

        $comment->setUpdatedAt($date);
        $this->assertEquals($date, $comment->getUpdatedAt());

        $comment->setContent('aaaa');
        $this->assertEquals('aaaa', $comment->getContent());

        /** @var MockObject&Message */
        $message = $this->getMockBuilder(Message::class)->getMock();
        $comment->setMessage($message);
        $this->assertEquals($message, $comment->getMessage());
    }
}
