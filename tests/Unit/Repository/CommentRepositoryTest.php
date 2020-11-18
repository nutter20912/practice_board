<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentRepositoryTest extends KernelTestCase
{
    public function testConstruct(): void
    {
        //arrange
        $repository = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Comment::class);

        //assert
        $this->assertInstanceOf(CommentRepository::class, $repository);
    }
}
