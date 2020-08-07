<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Exception;
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
        $sort->addToQuery($queryBuilder, 'db_field');

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
        $sort->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression */
        $orderBy = $queryBuilder->getQueryPart('orderBy');

        $this->assertSame(
            'db_table_alias.foo ASC',
            $orderBy[0]
        );
    }
}
