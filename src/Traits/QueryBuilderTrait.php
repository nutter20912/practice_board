<?php

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

trait QueryBuilderTrait
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $condition
     */
    private function setWhere(QueryBuilder $queryBuilder, $condition): void
    {
        $alias = $queryBuilder->getRootAlias();

        foreach ($condition as $key => $value) {
            $parameter = explode(':', $key)[1];
            $queryBuilder->andWhere("{$alias}.{$key}")
                ->setParameter($parameter, $value);
        }
    }
}
