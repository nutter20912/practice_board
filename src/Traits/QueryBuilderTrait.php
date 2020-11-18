<?php

namespace App\Traits;

use Doctrine\DBAL\Query\QueryException;
use Doctrine\ORM\QueryBuilder;

trait QueryBuilderTrait
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $condition
     *
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function setWhere(QueryBuilder $queryBuilder, $condition): void
    {
        $alias = $queryBuilder->getRootAlias();

        foreach ($condition as $key => $value) {
            if (!$parameter = $this->getParameter($key)) {
                throw new QueryException('key have to use colon explode.');
            }

            $queryBuilder->andWhere("{$alias}.{$key}")
                ->setParameter($parameter, $value);
        }
    }

    /**
     * @param string $key
     *
     * @return bool|string
     */
    private function getParameter($key)
    {
        if (strpos($key, ':') == 0) {
            return false;
        }

        return explode(':', $key)[1] ?? false;
    }
}
