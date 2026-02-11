<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class DataTypeTestCase extends TestCase
{
    protected function createQueryBuilderMock(): QueryBuilder
    {
        $connectionMock = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new QueryBuilder($connectionMock);
    }

    /**
     * Get a query part from QueryBuilder via reflection (DBAL 4 compatibility).
     *
     * In DBAL 4, getQueryPart() was removed. This method provides
     * a compatible way to access internal query parts for testing.
     *
     * @param QueryBuilder $builder
     * @param string $part The part name: 'where', 'orderBy', 'from', etc.
     * @return mixed
     */
    protected function getQueryPart(QueryBuilder $builder, string $part): mixed
    {
        $reflection = new ReflectionClass($builder);
        $property = $reflection->getProperty($part);
        return $property->getValue($builder);
    }
}
