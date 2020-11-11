<?php

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

trait PaginatorTrait
{
    /**
     * @var int $pageLimit
     */
    protected $pageLimit;

    /**
     * @var int $currentPage
     */
    protected $currentPage;

    /**
     * @return int
     */
    public function getPageLimit()
    {
        return $this->pageLimit;
    }

    /**
     * @param int $pageLimit
     *
     * @return self
     */
    public function setPageLimit($pageLimit): self
    {
        $this->pageLimit = $pageLimit;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     *
     * @return self
     */
    public function setCurrentPage($currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaginate(): bool
    {
        return $this->getPageLimit() && $this->getCurrentPage();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function setPaginate(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->setFirstResult($this->getPageLimit() * ($this->getCurrentPage() - 1))
            ->setMaxResults($this->getPageLimit());
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return int
     */
    public function getPages(QueryBuilder $queryBuilder)
    {
        $alias = $queryBuilder->getRootAlias();

        $total = $queryBuilder
            ->select("COUNT({$alias}.id)")
            ->getQuery()
            ->getSingleScalarResult();

        return $this->isPaginate()
            ? ceil($total / $this->getPageLimit())
            : 1;
    }
}
