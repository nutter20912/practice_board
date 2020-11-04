<?php

namespace App\Repository\Board;

use App\Entity\Board\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function store(array $data): void
    {
        $comment = new Comment();
        $comment->setName($data['name']);
        $comment->setContent($data['comment']);
        $comment->setMessage($data['message']);
        $comment->setCreatedAt(new \DateTime("now"));
        $comment->setUpdatedAt(new \DateTime("now"));

        $entityManager = $this->getEntityManager();
        $entityManager->persist($comment);
        $entityManager->flush();
    }

    /**
     * @param \App\Entity\Board\Comment $comment
     * @param string $content
     *
     * @return void
     */
    public function update(Comment $comment, string $content): void
    {
        $comment->setContent($content);
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }

    /**
     * @param \App\Entity\Board\Comment $comment
     *
     * @return void
     */
    public function delete(Comment $comment): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($comment);
        $entityManager->flush();
    }
}
