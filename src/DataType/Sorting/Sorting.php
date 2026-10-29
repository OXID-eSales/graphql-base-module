<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Sorting;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use OxidEsales\GraphQL\Base\Exception\InvalidArgumentMultiplePossible;

abstract class Sorting
{
    public const SORTING_DESC = 'DESC';

    public const SORTING_ASC = 'ASC';

    private const ALLOWED_DIRECTIONS = [self::SORTING_ASC, self::SORTING_DESC];

    /** @var array<string, string> */
    private array $sorting;

    /**
     * @param array<string, null|string> $sorting
     */
    public function __construct(array $sorting)
    {
        $this->sorting = array_filter($sorting);

        foreach ($this->sorting as $field => $val) {
            if (!in_array($val, self::ALLOWED_DIRECTIONS, true)) {
                throw new InvalidArgumentMultiplePossible($field, self::ALLOWED_DIRECTIONS, $val);
            }
        }
    }

    public function addToQuery(QueryBuilder $builder): void
    {
        /** @var array $from */
        $from = $builder->getQueryPart('from');

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }
        $table = $from[0]['alias'] ?? $from[0]['table'];

        foreach ($this->sorting as $field => $dir) {
            $builder->addOrderBy($table . '.' . $field, $dir);
        }
    }

    public function isEmpty(): bool
    {
        return $this->sorting === [];
    }
}
