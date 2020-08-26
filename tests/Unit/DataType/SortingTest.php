<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Exception;
use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Sorting;

class SortingTest extends DataTypeTestCase
{
    public function testThrowsExceptionOnInvalidInput(): void
    {
        $this->expectException(Exception::class);
        new class(['foo' => 'bar']) extends Sorting {
        };
    }

    public function testAddQueryPartWithoutAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort         = new class(['foo' => 'ASC']) extends Sorting {
        };

        $queryBuilder->select()->from('db_table');
        $sort->addToQuery($queryBuilder);

        /** @var CompositeExpression */
        $orderBy = $queryBuilder->getQueryPart('orderBy');

        $this->assertSame(
            'db_table.foo ASC',
            $orderBy[0]
        );
    }

    public function testAddQueryPartWithAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort         = new class(['foo' => 'ASC']) extends Sorting {
        };

        $queryBuilder->select()->from('db_table', 'db_table_alias');
        $sort->addToQuery($queryBuilder);

        /** @var CompositeExpression */
        $orderBy = $queryBuilder->getQueryPart('orderBy');

        $this->assertSame(
            'db_table_alias.foo ASC',
            $orderBy[0]
        );
    }

    public function testAddQueryWithMultipleSearchFields(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort         = new class(['foo' => 'ASC', 'bar' => 'DESC', 'empty' => null]) extends Sorting {
        };

        $queryBuilder->select()->from('db_table');
        $sort->addToQuery($queryBuilder);

        /** @var CompositeExpression */
        $orderBy = $queryBuilder->getQueryPart('orderBy');

        $this->assertCount(
            2,
            $orderBy
        );
        $this->assertSame(
            'db_table.foo ASC',
            $orderBy[0]
        );
        $this->assertSame(
            'db_table.bar DESC',
            $orderBy[1]
        );
    }

    public function testFailAddToQueryWithoutFormPart(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $sort = new class([]) extends Sorting {
        };

        $this->expectException(InvalidArgumentException::class);
        $sort->addToQuery($queryBuilder);
    }
}
