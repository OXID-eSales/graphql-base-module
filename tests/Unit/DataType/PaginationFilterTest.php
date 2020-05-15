<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;

class PaginationFilterTest extends DataTypeTestCase
{
    public function testReturnOnEmptyInitialization(): void
    {
        $this->assertSame(
            0,
            (new PaginationFilter())->offset()
        );
        $this->assertSame(
            null,
            (new PaginationFilter())->limit()
        );
    }

    public function testBasicPaginationFilter(): void
    {
        $filter = PaginationFilter::fromUserInput(
            1,
            2
        );
        $this->assertSame(
            1,
            $filter->offset()
        );
        $this->assertSame(
            2,
            $filter->limit()
        );
    }

    public function testDefaultNamedConstructor(): void
    {
        $paging = PaginationFilter::fromUserInput();

        $this->assertSame(
            0,
            $paging->offset()
        );
        $this->assertNull(
            $paging->limit()
        );
    }

    /**
     * @dataProvider paginationDataProvider
     *
     * @param mixed $offset
     * @param mixed $limit
     */
    public function testInvalidValuesOnPaginationFilter($offset, $limit): void
    {
        $this->expectExceptionMessage('PaginationFilter fields must be positive.');

        $filter = PaginationFilter::fromUserInput($offset, $limit);
        $filter->offset();
        $filter->limit();
    }

    public function paginationDataProvider(): array
    {
        return [
            [0, 0],
            [0, -1],
            [-1, 1],
            [-1, null],
        ];
    }

    /**
     * @dataProvider addPaginationToQueryProvider
     */
    public function testAddPaginationToQuery(int $offset, ?int $limit): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter       = PaginationFilter::fromUserInput($offset, $limit);

        $filter->addPaginationToQuery($queryBuilder);

        $this->assertEquals($offset, $queryBuilder->getFirstResult());
        $this->assertEquals($limit, $queryBuilder->getMaxResults());
    }

    public function addPaginationToQueryProvider(): array
    {
        return [
            [0, null], [0, 100], [5, null], [100, 10],
        ];
    }
}
