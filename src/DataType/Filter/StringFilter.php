<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function strtoupper;

class StringFilter implements FilterInterface
{
    use QueryBuilderHelper;
    public function __construct(
        private readonly ?string $equals = null,
        private readonly ?string $contains = null,
        private readonly ?string $beginsWith = null
    ) {
        if (
            $equals === null &&
            $contains === null &&
            $beginsWith === null
        ) {
            throw new Error('At least one field for type StringFilter must be provided');
        }
    }

    public function equals(): ?string
    {
        return $this->equals;
    }

    public function contains(): ?string
    {
        return $this->contains;
    }

    public function matches(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        if ($this->equals !== null && strcasecmp($value, $this->equals) !== 0) {
            return false;
        }

        if ($this->contains !== null && stripos($value, $this->contains) === false) {
            return false;
        }

        if ($this->beginsWith !== null && strncasecmp($value, $this->beginsWith, strlen($this->beginsWith)) !== 0) {
            return false;
        }

        return true;
    }

    public function beginsWith(): ?string
    {
        return $this->beginsWith;
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $table = $this->getFromTableAlias($builder);

        if ($this->equals) {
            $builder->andWhere(sprintf('%s.%s = :%s_eq', $table, strtoupper($field), $field))
                ->setParameter(':' . $field . '_eq', $this->equals);
            // if equals is set, then no other conditions may apply
            return;
        }

        if ($this->contains) {
            $builder->andWhere(sprintf('%s.%s LIKE :%s_contain', $table, strtoupper($field), $field))
                ->setParameter(':' . $field . '_contain', '%' . $this->contains . '%');
        }

        if ($this->beginsWith) {
            $builder->andWhere(sprintf('%s.%s LIKE :%s_begins', $table, strtoupper($field), $field))
                ->setParameter(':' . $field . '_begins', $this->beginsWith . '%');
        }
    }

    /**
     * @Factory(name="StringFilterInput", default=true)
     */
    public static function fromUserInput(
        ?string $equals = null,
        ?string $contains = null,
        ?string $beginsWith = null
    ): self {
        return new self(
            $equals,
            $contains,
            $beginsWith
        );
    }
}
