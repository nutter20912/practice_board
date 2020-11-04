<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
    public function getMessagePaginator($page, $limit)
    {
        $query = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery();

        return [
            (new Paginator($query))->count(),
            $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY)
        ];
    }
}
