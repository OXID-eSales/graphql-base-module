<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\Tests\Unit\DataType\DataTypeTestCase;
use TheCodingMachine\GraphQLite\Types\ID;

class IDFilterTest extends DataTypeTestCase
{
    public function testBasicIDFilter(): void
    {
        $filter = IDFilter::fromUserInput(
            new ID('test')
        );
        $this->assertSame(
            'test',
            (string)$filter->equals()
        );
    }

    public function testAddQueryPartWithNoFrom(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter = IDFilter::fromUserInput(new ID('106d2528c6a9796fbd13cd30de6decf1'));

        $this->expectException(InvalidArgumentException::class);
        $filter->addToQuery($queryBuilder, 'db_field', '');
    }

    public function testAddQueryPart(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $id = '106d2528c6a9796fbd13cd30de6decf1';
        $filter = IDFilter::fromUserInput(new ID($id));
        $filter->setFrom('db_table');

        $queryBuilder->select('*')->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        $query = $queryBuilder->getSQL();

        $this->assertStringContainsString('db_table.DB_FIELD = :db_field', $query);
        $this->assertSame($id, (string) $queryBuilder->getParameter('db_field'));
    }

    public function testAddQueryPartWithAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter = IDFilter::fromUserInput(new ID('106d2528c6a9796fbd13cd30de6decf1'));
        $filter->setFrom('db_table_alias');

        $queryBuilder->select('*')->from('db_table', 'db_table_alias');
        $filter->addToQuery($queryBuilder, 'db_field');

        $query = $queryBuilder->getSQL();

        $this->assertStringContainsString('db_table_alias.DB_FIELD = :db_field', $query);
    }
}
