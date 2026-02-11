<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use ReflectionClass;

/**
 * Helper trait for DBAL 4 compatibility.
 *
 * In DBAL 4, QueryBuilder::getQueryPart() was removed.
 * This trait provides a compatible way to get the FROM table/alias.
 */
trait QueryBuilderHelper
{
    /**
     * Get the table name or alias from the QueryBuilder's FROM clause.
     *
     * @throws InvalidArgumentException if QueryBuilder has no FROM clause
     */
    protected function getFromTableAlias(QueryBuilder $builder): string
    {
        // DBAL 4: getQueryPart() was removed, use reflection to access private $from
        $reflection = new ReflectionClass($builder);
        $property = $reflection->getProperty('from');
        $from = $property->getValue($builder);

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }

        // DBAL 4 uses From objects with public table/alias properties
        $firstFrom = $from[0];
        if (is_object($firstFrom)) {
            return $firstFrom->alias ?? $firstFrom->table;
        }

        // DBAL 3 fallback (array format)
        return $firstFrom['alias'] ?? $firstFrom['table'];
    }
}
