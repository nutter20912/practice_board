<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Traits\PaginatorTrait;
use App\Traits\QueryBuilderTrait;

class MessageRepository extends ServiceEntityRepository
{
    use PaginatorTrait;
    use QueryBuilderTrait;

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
    public function getMessagePaginator()
    {
        $queryBuilder = $this->createQueryBuilder('m');

        if ($this->isPaginate()) {
            $this->setPaginate($queryBuilder);
        }

        return $queryBuilder
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return int
     */
    public function getMessagePages()
    {
        $queryBuilder = $this->createQueryBuilder('m');

        return $this->getPages($queryBuilder);
    }
}
