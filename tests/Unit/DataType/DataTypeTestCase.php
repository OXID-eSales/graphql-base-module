<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;

abstract class DataTypeTestCase extends TestCase
{
    protected function createQueryBuilderMock(): QueryBuilder
    {
        $connectionMock = $this->createPartialMock(
            Connection::class,
            ['connect', 'getDatabasePlatform']
        );
        $connectionMock->expects($this->any())
            ->method('getDatabasePlatform')
            ->willReturn(new MySqlPlatform());

        return new QueryBuilder($connectionMock);
    }
}
