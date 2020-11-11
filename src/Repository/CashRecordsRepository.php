<?php

namespace App\Repository;

use App\Entity\CashRecords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Traits\PaginatorTrait;
use App\Traits\QueryBuilderTrait;

/**
 * @method CashRecords|null find($id, $lockMode = null, $lockVersion = null)
 * @method CashRecords|null findOneBy(array $criteria, array $orderBy = null)
 * @method CashRecords[]    findAll()
 * @method CashRecords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CashRecordsRepository extends ServiceEntityRepository
{
    use PaginatorTrait;
    use QueryBuilderTrait;

    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashRecords::class);
    }

    /**
     * @param array $condition
     *
     * @return array
     */
    public function getRecordsByDate($condition)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $this->setWhere($queryBuilder, $condition);

        if ($this->isPaginate()) {
            $this->setPaginate($queryBuilder);
        }

        $alias = $queryBuilder->getRootAlias();

        $fields = [
            "{$alias}.operator",
            "{$alias}.diff",
            "{$alias}.current",
            "{$alias}.ip",
            "{$alias}.created_at",
        ];

        return $queryBuilder
            ->select($fields)
            ->orderBy("{$alias}.id", 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param array $condition
     *
     * @return int
     */
    public function getRecordsPages($condition)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $this->setWhere($queryBuilder, $condition);

        return $this->getPages($queryBuilder);
    }
}
