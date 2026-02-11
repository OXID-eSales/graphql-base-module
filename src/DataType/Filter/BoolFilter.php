<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function strtoupper;

class BoolFilter implements FilterInterface
{
    use QueryBuilderHelper;
    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(private readonly bool $equals = true)
    {
    }

    public function equals(): bool
    {
        return $this->equals;
    }

    public function matches(mixed $value): bool
    {
        return (is_bool($value) && $value === $this->equals);
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $table = $this->getFromTableAlias($builder);

        $builder->andWhere(sprintf('%s.%s = :%s', $table, strtoupper($field), $field))
            ->setParameter(':' . $field, $this->equals ? '1' : '0');
        // if equals is set, then no other conditions may apply
    }

    /**
     * @Factory(name="BoolFilterInput", default=true)
     */
    public static function fromUserInput(
        bool $equals
    ): self {
        return new self(
            $equals
        );
    }
}
