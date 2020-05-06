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
use OxidEsales\GraphQL\Base\DataType\StringFilter;

class StringFilterTest extends DataTypeTestCase
{
    public function testThrowsExceptionOnNoInput(): void
    {
        $this->expectException(Exception::class);
        StringFilter::fromUserInput();
    }

    public function testNeedsAtLeastOneParameter(): void
    {
        $this->assertSame(
            'equals',
            (StringFilter::fromUserInput('equals'))->equals()
        );

        $this->assertSame(
            'contains',
            (StringFilter::fromUserInput(null, 'contains'))->contains()
        );

        $this->assertSame(
            'beginsWith',
            (StringFilter::fromUserInput(null, null, 'beginsWith'))->beginsWith()
        );
    }

    public function testBasicStringFilter(): void
    {
        $filter = StringFilter::fromUserInput(
            'equals',
            'contains',
            'beginsWith'
        );
        $this->assertSame(
            'equals',
            $filter->equals()
        );
        $this->assertSame(
            'contains',
            $filter->contains()
        );
        $this->assertSame(
            'beginsWith',
            $filter->beginsWith()
        );
    }

    public function testAddQueryPartWithNoFrom(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter       = StringFilter::fromUserInput('no_from');

        $this->expectException(InvalidArgumentException::class);
        $filter->addToQuery($queryBuilder, 'db_field');
    }

    public function testAddQueryPartEquals(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $string = 'equals';
        $filter = StringFilter::fromUserInput($string);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals('db_table.DB_FIELD = :db_field_eq', (string) $where);
        $this->assertEquals($string, $queryBuilder->getParameter(':db_field_eq'));
    }

    public function testAddQueryPartContains(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $string = 'contains';
        $filter = StringFilter::fromUserInput(null, $string);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals(
            'db_table.DB_FIELD LIKE :db_field_contain',
            (string) $where
        );
        $this->assertEquals('%' . $string . '%', $queryBuilder->getParameter(':db_field_contain'));
    }

    public function testAddQueryPartBegins(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $string = 'begins';
        $filter = StringFilter::fromUserInput(null, null, $string);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals(
            'db_table.DB_FIELD LIKE :db_field_begins',
            (string) $where
        );
        $this->assertEquals($string . '%', $queryBuilder->getParameter(':db_field_begins'));
    }

    public function testAddQueryPartWithAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter       = StringFilter::fromUserInput('with_alias');

        $queryBuilder->select()->from('db_table', 'db_table_alias');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals('db_table_alias.DB_FIELD = :db_field_eq', (string) $where);
    }
}
