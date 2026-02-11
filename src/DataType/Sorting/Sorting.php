<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Sorting;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\GraphQL\Base\DataType\Filter\QueryBuilderHelper;
use OxidEsales\GraphQL\Base\Exception\InvalidArgumentMultiplePossible;

abstract class Sorting
{
    use QueryBuilderHelper;
    public const SORTING_DESC = 'DESC';

    public const SORTING_ASC = 'ASC';

    /** @var array<string, null|string> */
    private array $sorting;

    /**
     * @param array<string, null|string> $sorting
     */
    public function __construct(array $sorting)
    {
        $this->sorting = array_filter($sorting);

        foreach ($this->sorting as $field => $val) {
            if (
                $val !== self::SORTING_DESC &&
                $val !== self::SORTING_ASC
            ) {
                throw new InvalidArgumentMultiplePossible($field, ['ASC', 'DESC'], $val);
            }
        }
    }

    public function addToQuery(QueryBuilder $builder): void
    {
        $table = $this->getFromTableAlias($builder);

        foreach ($this->sorting as $field => $dir) {
            $builder->addOrderBy($table . '.' . $field, $dir);
        }
    }
}
