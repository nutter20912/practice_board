<?php

namespace App\Repository\Board;

use App\Entity\Board\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return Message[] Returns an array of Message objects
     */
    public function getMessagePaginator(int $page, int $limit): ?Paginator
    {
        $query = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function store(array $data): void
    {
        $message = new Message();
        $message->setAuthor($data['author']);
        $message->setTitle($data['title']);
        $message->setContent($data['content']);
        $message->setCreatedAt(new \DateTime("now"));
        $message->setUpdatedAt(new \DateTime("now"));

        $entityManager = $this->getEntityManager();
        $entityManager->persist($message);
        $entityManager->flush();
    }

    /**
     * @param \App\Entity\Board\Message $message
     * @param string $content
     *
     * @return void
     */
    public function update(Message $message, string $content): void
    {
        $message->setContent($content);

        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }

    /**
     * @param \App\Entity\Board\Message $message
     *
     * @return void
     */
    public function delete(Message $message): void
    {
        $message->getComment()->removeElement($message);

        $entityManager = $this->getEntityManager();
        $entityManager->remove($message);
        $entityManager->flush();
    }
}
