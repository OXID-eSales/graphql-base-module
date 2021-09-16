<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;
use InvalidArgumentException;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function strtoupper;

class StringFilter implements FilterInterface
{
    /** @var ?string */
    private $equals;

    /** @var ?string */
    private $contains;

    /** @var ?string */
    private $beginsWith;

    public function __construct(
        ?string $equals = null,
        ?string $contains = null,
        ?string $beginsWith = null
    ) {
        if (
            $equals === null &&
            $contains === null &&
            $beginsWith === null
        ) {
            throw new Error('At least one field for type StringFilter must be provided');
        }
        $this->equals     = $equals;
        $this->contains   = $contains;
        $this->beginsWith = $beginsWith;
    }

    public function equals(): ?string
    {
        return $this->equals;
    }

    public function contains(): ?string
    {
        return $this->contains;
    }

    public function beginsWith(): ?string
    {
        return $this->beginsWith;
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $from = $builder->getQueryPart('from');

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }
        $table = $from[0]['alias'] ?? $from[0]['table'];

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
     * @Factory(name="StringFilterInput")
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
