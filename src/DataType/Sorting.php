<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;

abstract class Sorting
{
    public const SORTING_DESC = 'DESC';

    public const SORTING_ASC  = 'ASC';

    /** @var array<string, null|string> */
    private $sorting;

    /**
     * @param array<string, null|string> $sorting
     */
    public function __construct(
        array $sorting
    ) {
        $sorting = array_filter($sorting);

        foreach ($sorting as $field => $val) {
            if (
                $val !== self::SORTING_DESC &&
                $val !== self::SORTING_ASC
            ) {
                throw new InvalidArgumentException('"' . $field . '" is only allowed to be one of ASC, DESC, was "' . $val . '"');
            }
        }
        $this->sorting = $sorting;
    }

    public function addToQuery(QueryBuilder $builder): void
    {
        $from = $builder->getQueryPart('from');

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }
        $table = $from[0]['alias'] ?? $from[0]['table'];

        foreach ($this->sorting as $field => $dir) {
            $builder->addOrderBy($table . '.' . $field, $dir);
        }
    }
}
