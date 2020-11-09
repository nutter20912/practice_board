<?php

namespace App\Repository;

use App\Entity\CashRecords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CashRecords|null find($id, $lockMode = null, $lockVersion = null)
 * @method CashRecords|null findOneBy(array $criteria, array $orderBy = null)
 * @method CashRecords[]    findAll()
 * @method CashRecords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CashRecordsRepository extends ServiceEntityRepository
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashRecords::class);
    }

    /**
     * @param array $condition
     * @param int $limit
     * @param int $page
     *
     * @return \Doctrine\ORM\Query
     */
    public function getRecordsByDate($condition , $limit, $page): Query
    {
        $fields = [
            'c.operator',
            'c.diff',
            'c.current',
            'c.ip',
            'c.created_at',
        ];

        return $this->createQueryBuilder('c')
            ->select($fields)
            ->where('c.user_id = :id')
            ->andWhere('c.created_at > :start')
            ->andWhere('c.created_at < :end')
            ->orderBy('c.id', 'DESC')
            ->setParameters($condition)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery();
    }
}
