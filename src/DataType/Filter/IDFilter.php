<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

use function strtoupper;

class IDFilter implements FilterInterface
{
    use QueryBuilderHelper;
    public function __construct(private readonly ID $equals)
    {
    }

    public function equals(): ID
    {
        return $this->equals;
    }

    public function matches(mixed $value): bool
    {
        if ($value instanceof ID) {
            $value = $value->val();
        }

        return (is_string($value) && $this->equals->val() === $value);
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $table = $this->getFromTableAlias($builder);

        $builder->andWhere(sprintf('%s.%s = :%s', $table, strtoupper($field), $field))
            ->setParameter(':' . $field, $this->equals);
    }

    /**
     * @Factory(name="IDFilterInput", default=true)
     */
    public static function fromUserInput(
        ID $equals
    ): self {
        return new self(
            $equals
        );
    }
}
