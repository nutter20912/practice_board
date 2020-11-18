<?php

namespace App\Tests\Unit\Traits;

use App\Traits\PaginatorTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaginatorTraitTest extends TestCase
{
    public function testGetterSetter(): void
    {
        //arrange
        /** @var MockObject&PaginatorTrait */
        $PaginatorTraitMock = $this->getMockForTrait(PaginatorTrait::class);
        $pageLimit = 1;
        $currentPage = 1;

        //act
        $PaginatorTraitMock->setPageLimit($pageLimit);
        $PaginatorTraitMock->setCurrentPage($currentPage);

        //assert
        $this->assertEquals($pageLimit, $PaginatorTraitMock->getPageLimit());
        $this->assertIsInt($PaginatorTraitMock->getPageLimit());
        $this->assertEquals($currentPage, $PaginatorTraitMock->getCurrentPage());
        $this->assertIsInt($PaginatorTraitMock->getCurrentPage());
    }

    public function testIsPaginate(): void
    {
        //arrange
        /** @var MockObject&PaginatorTrait */
        $PaginatorTraitMock = $this->getMockForTrait(PaginatorTrait::class);
        $pageLimit = 1;
        $currentPage = 1;

        //act
        $PaginatorTraitMock->setPageLimit($pageLimit);
        $PaginatorTraitMock->setCurrentPage($currentPage);
        $res = $PaginatorTraitMock->isPaginate();

        //assert
        $this->assertTrue($res);
    }

    public function testNotPaginate(): void
    {
        //arrange
        /** @var MockObject&PaginatorTrait */
        $PaginatorTraitMock = $this->getMockForTrait(PaginatorTrait::class);

        //act
        $res = $PaginatorTraitMock->isPaginate();

        //assert
        $this->assertFalse($res);
    }

    public function testSetPaginate(): void
    {
        //arrange
        /** @var MockObject&QueryBuilder */
        $QueryBuilderMock = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $QueryBuilderMock
            ->method('setFirstResult')
            ->will($this->returnSelf());
        $QueryBuilderMock
            ->method('setMaxResults')
            ->will($this->returnSelf());

        /** @var MockObject&PaginatorTrait */
        $PaginatorTraitMock = $this->getMockForTrait(PaginatorTrait::class);

        //act
        $res = $PaginatorTraitMock->setPaginate($QueryBuilderMock);

        //assert
        $this->assertNull($res);
    }

    public function successProvider()
    {
        return [
            [10, 3, 1],
            [30, 3, 1],
        ];
    }
    /**
     * @dataProvider successProvider
     */
    public function testGetPaginatePage($total, $pageLimit, $currentPage): void
    {
        //arrange
        /** @var MockObject&Query */
        $queryMock = $this
            ->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryMock
            ->method('getSingleScalarResult')
            ->willReturn($total);

        /** @var MockObject&QueryBuilder */
        $QueryBuilderMock = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $QueryBuilderMock
            ->method('getRootAlias')
            ->willReturn('alias');
        $QueryBuilderMock
            ->method('select')
            ->will($this->returnSelf());
        $QueryBuilderMock
            ->method('getQuery')
            ->willReturn($queryMock);

        /** @var MockObject&PaginatorTrait */
        $PaginatorTraitMock = $this->getMockForTrait(PaginatorTrait::class);

        //act
        $PaginatorTraitMock->setPageLimit($pageLimit);
        $PaginatorTraitMock->setCurrentPage($currentPage);
        $response = $PaginatorTraitMock->getPages($QueryBuilderMock);

        //assert
        $this->assertIsInt($response);
        $this->assertEquals(ceil($total / $pageLimit), $response);
    }

    /**
     * @dataProvider successProvider
     */
    public function testGetNotPaginatePage(): void
    {
        //arrange
        $total = 10;

        /** @var MockObject&Query */
        $queryMock = $this
            ->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryMock
            ->method('getSingleScalarResult')
            ->willReturn($total);

        /** @var MockObject&QueryBuilder */
        $QueryBuilderMock = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $QueryBuilderMock
            ->method('getRootAlias')
            ->willReturn('alias');
        $QueryBuilderMock
            ->method('select')
            ->will($this->returnSelf());
        $QueryBuilderMock
            ->method('getQuery')
            ->willReturn($queryMock);

        /** @var MockObject&PaginatorTrait */
        $PaginatorTraitMock = $this->getMockForTrait(PaginatorTrait::class);

        //act
        $response = $PaginatorTraitMock->getPages($QueryBuilderMock);

        //assert
        $this->assertIsInt($response);
        $this->assertEquals(1, $response);
    }
}
