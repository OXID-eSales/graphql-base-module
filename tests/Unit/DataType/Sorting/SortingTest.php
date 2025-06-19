<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Sorting;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting;
use OxidEsales\GraphQL\Base\Exception\InvalidArgumentMultiplePossible;
use OxidEsales\GraphQL\Base\Tests\Unit\DataType\DataTypeTestCase;

class SortingTest extends DataTypeTestCase
{
    public function testThrowsExceptionOnInvalidInput(): void
    {
        $this->expectException(InvalidArgumentMultiplePossible::class);
        $this->expectExceptionMessage('"foo" is only allowed to be one of "ASC, DESC", was "bar"');
        new class (['foo' => 'bar']) extends Sorting {
        };
    }

    public function testAddQueryPartWithoutAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort = new class (['foo' => 'ASC']) extends Sorting {
        };

        $queryBuilder->select('*')->from('db_table');
        $sort->setFrom('db_table');
        $sort->addToQuery($queryBuilder);

        $query = $queryBuilder->getSQL();

        $this->assertStringContainsString(
            'db_table.foo ASC',
            $query
        );
    }

    public function testAddQueryPartWithAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort = new class (['foo' => 'ASC']) extends Sorting {
        };

        $queryBuilder->select('*')->from('db_table', 'db_table_alias');
        $sort->setFrom('db_table_alias');
        $sort->addToQuery($queryBuilder);

        $query = $queryBuilder->getSQL();

        $this->assertStringContainsString(
            'db_table_alias.foo ASC',
            $query
        );
    }

    public function testAddQueryWithMultipleSearchFields(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort = new class (['foo' => 'ASC', 'bar' => 'DESC', 'empty' => null]) extends Sorting {
        };

        $queryBuilder->select('*')->from('db_table');
        $sort->setFrom('db_table');
        $sort->addToQuery($queryBuilder);

        $query = $queryBuilder->getSQL();

        $this->assertStringContainsString(
            'db_table.foo ASC',
            $query
        );
        $this->assertStringContainsString(
            'db_table.bar DESC',
            $query
        );
    }

    public function testFailAddToQueryWithoutFormPart(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $sort = new class ([]) extends Sorting {
        };

        $this->expectException(InvalidArgumentException::class);
        $sort->addToQuery($queryBuilder, '');
    }

    public function testFailOnWrongSortingConfiguration(): void
    {
        $this->expectException(InvalidArgumentMultiplePossible::class);
        $this->expectExceptionMessage('"foo" is only allowed to be one of "ASC, DESC", was "x"');
        new class (['foo' => 'x']) extends Sorting {
        };
    }
}
