<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;

use function strtoupper;

class FloatFilterInput implements FilterInputInterface
{
    /** @var ?float */
    private $equals;

    /** @var ?float */
    private $lowerThen;

    /** @var ?float */
    private $greaterThen;

    /** @var array{0: float, 1: float}|null */
    private $between;

    /**
     * @param array{0: float, 1: float}|null $between
     */
    public function __construct(
        ?float $equals = null,
        ?float $lowerThen = null,
        ?float $greaterThen = null,
        ?array $between = null
    ) {
        if (
            $equals === null &&
            $lowerThen === null &&
            $greaterThen === null &&
            $between === null
        ) {
            throw new Error("At least one field for type FloatFilterInput must be provided");
        }
        $this->equals      = $equals;
        $this->lowerThen   = $lowerThen;
        $this->greaterThen = $greaterThen;
        $this->between     = $between;
    }

    public function equals(): ?float
    {
        return $this->equals;
    }

    public function lowerThen(): ?float
    {
        return $this->lowerThen;
    }

    public function greaterThen(): ?float
    {
        return $this->greaterThen;
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    public function between(): ?array
    {
        return $this->between;
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        if ($this->equals) {
            $builder->andWhere(strtoupper($field) . ' = :' . $field . '_eq')
                    ->setParameter(':' . $field . '_eq', $this->equals);
            // if equals is set, then no other conditions may apply
            return;
        }
        if ($this->lowerThen) {
            $builder->andWhere(strtoupper($field) . ' < :' . $field . '_lt')
                    ->setParameter(':' . $field . '_lt', $this->lowerThen);
        }
        if ($this->greaterThen) {
            $builder->andWhere(strtoupper($field) . ' > :' . $field . '_gt')
                    ->setParameter(':' . $field . '_gt', $this->greaterThen);
        }
        if ($this->between) {
            $builder->andWhere(strtoupper($field) . ' BETWEEN :' . $field . '_lower AND :' . $field . '_upper')
                    ->setParameter(':' . $field . '_lower', $this->between[0])
                    ->setParameter(':' . $field . '_upper', $this->between[1]);
        }
    }
}
