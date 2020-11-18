<?php

namespace App\Unit\Traits;

use App\Traits\QueryBuilderTrait;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;

class QueryBuilderTraitTest extends TestCase
{
    public function testSetWhereSeccess(): void
    {
        //arrange
        /** @var MockObject&QueryBuilder */
        $queryBuilderMock = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilderMock
            ->method('getRootAlias')
            ->willReturn('alias');
        $queryBuilderMock
            ->method('andWhere')
            ->will($this->returnSelf());
        $queryBuilderMock
            ->method('setParameter')
            ->will($this->returnSelf());

        /** @var MockObject&QueryBuilderTrait */
        $paginatorTraitMock = $this->getMockForTrait(QueryBuilderTrait::class);

        //act
        $response = $paginatorTraitMock
            ->setWhere($queryBuilderMock, [
                'id :id' => 1,
                'name :name' => 'paul',
            ]);

        //assert
        $this->assertNull($response);
    }

    /**
     * @expectedException Doctrine\DBAL\Query\QueryException
     */
    public function testSetWhereByWrongCondition(): void
    {
        //arrange
        /** @var MockObject&QueryBuilder */
        $queryBuilderMock = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilderMock
            ->method('getRootAlias')
            ->willReturn('alias');
        $queryBuilderMock
            ->method('andWhere')
            ->will($this->returnSelf());
        $queryBuilderMock
            ->method('setParameter')
            ->will($this->returnSelf());

        /** @var MockObject&QueryBuilderTrait */
        $paginatorTraitMock = $this->getMockForTrait(QueryBuilderTrait::class);

        //act
        $paginatorTraitMock->setWhere($queryBuilderMock, ['name' => 'paul']);
    }
}
