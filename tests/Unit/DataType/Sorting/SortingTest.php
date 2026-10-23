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
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;

#[AllowMockObjectsWithoutExpectations]
class SortingTest extends DataTypeTestCase
{
    #[Test]
    public function throwsExceptionOnInvalidInput(): void
    {
        $this->expectException(InvalidArgumentMultiplePossible::class);
        $this->expectExceptionMessage('"foo" is only allowed to be one of "ASC, DESC", was "bar"');
        new class (['foo' => 'bar']) extends Sorting {
        };
    }

    #[Test]
    public function addQueryPartWithoutAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort = new class (['foo' => 'ASC']) extends Sorting {
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

    #[Test]
    public function addQueryPartWithAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort = new class (['foo' => 'ASC']) extends Sorting {
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

    #[Test]
    public function addQueryWithMultipleSearchFields(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $sort = new class (['foo' => 'ASC', 'bar' => 'DESC', 'empty' => null]) extends Sorting {
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

    #[Test]
    public function failAddToQueryWithoutFromPart(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $sort = new class ([]) extends Sorting {
        };

        $this->expectException(InvalidArgumentException::class);
        $sort->addToQuery($queryBuilder);
    }

    #[Test]
    public function isEmptyWhenAllFieldsAreFilteredAway(): void
    {
        $sut = new class ([uniqid('emptyField') => '', uniqid('nullField') => null]) extends Sorting {
        };

        $this->assertTrue($sut->isEmpty());
    }

    #[Test]
    public function isNotEmptyWhenAnyFieldIsSorted(): void
    {
        $sut = new class ([uniqid('sortedField') => 'ASC', uniqid('emptyField') => '']) extends Sorting {
        };

        $this->assertFalse($sut->isEmpty());
    }

    #[Test]
    public function failOnWrongSortingConfiguration(): void
    {
        $this->expectException(InvalidArgumentMultiplePossible::class);
        $this->expectExceptionMessage('"foo" is only allowed to be one of "ASC, DESC", was "x"');
        new class (['foo' => 'x']) extends Sorting {
        };
    }
}
