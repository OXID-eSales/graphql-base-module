<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;

class StringFilterInput implements FilterInputInterface
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
            throw new Error("At least one field for type StringFilterInput must be provided");
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
        if ($this->equals) {
            $builder->andWhere(strtoupper($field) . ' = :' . $field . '_eq')
                    ->setParameter(':' . $field . '_eq', $this->equals);
            // if equals is set, then no other conditions may apply
            return;
        }
        if ($this->contains) {
            $builder->andWhere(strtoupper($field) . ' LIKE :' . $field . '_contain')
                    ->setParameter(':' . $field . '_contain', '%' . $this->contains . '%');
        }
        if ($this->beginsWith) {
            $builder->andWhere(strtoupper($field) . ' LIKE :' . $field . '_begins')
                    ->setParameter(':' . $field . '_begins', $this->beginsWith . '%');
        }
    }
}
