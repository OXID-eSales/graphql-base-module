<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;
use OutOfBoundsException;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function count;

class IntegerFilter implements FilterInterface
{
    /** @var ?int */
    private $equals;

    /** @var ?int */
    private $lowerThen;

    /** @var ?int */
    private $greaterThen;

    /** @var array{0: int, 1: int}|null */
    private $between;

    /**
     * @param array{0: int, 1: int}|null $between
     */
    public function __construct(
        ?int $equals = null,
        ?int $lowerThen = null,
        ?int $greaterThen = null,
        ?array $between = null
    ) {
        if (
            $equals === null &&
            $lowerThen === null &&
            $greaterThen === null &&
            $between === null
        ) {
            throw new Error('At least one field for type IntegerFilter must be provided');
        }
        $this->equals      = $equals;
        $this->lowerThen   = $lowerThen;
        $this->greaterThen = $greaterThen;
        $this->between     = $between;
    }

    public function equals(): ?int
    {
        return $this->equals;
    }

    public function lowerThen(): ?int
    {
        return $this->lowerThen;
    }

    public function greaterThen(): ?int
    {
        return $this->greaterThen;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    public function between(): ?array
    {
        return $this->between;
    }

    public function addToQuery(QueryBuilder $builder, string $field, string $fromAlias): void
    {
        if ($this->equals) {
            $builder->andWhere(sprintf("%s.%s = :%s_eq", $fromAlias, strtoupper($field), $field))
                    ->setParameter(':' . $field . '_eq', $this->equals);
            // if equals is set, then no other conditions may apply
            return;
        }

        if ($this->lowerThen) {
            $builder->andWhere(sprintf("%s.%s < :%s_lt", $fromAlias, strtoupper($field), $field))
                    ->setParameter(':' . $field . '_lt', $this->lowerThen);
        }

        if ($this->greaterThen) {
            $builder->andWhere(sprintf("%s.%s > :%s_gt", $fromAlias, strtoupper($field), $field))
                    ->setParameter(':' . $field . '_gt', $this->greaterThen);
        }

        if ($this->between) {
            $builder->andWhere(sprintf("%s.%s BETWEEN :%s_lower AND :%s_upper", $fromAlias, strtoupper($field), $field, $field))
                    ->setParameter(':' . $field . '_lower', $this->between[0])
                    ->setParameter(':' . $field . '_upper', $this->between[1]);
        }
    }

    /**
     * @Factory(name="IntegerFilterInput")
     *
     * @param null|int[] $between
     */
    public static function fromUserInput(
        ?int $equals = null,
        ?int $lowerThen = null,
        ?int $greaterThen = null,
        ?array $between = null
    ): self {
        if (
            $between !== null && (
                count($between) !== 2 ||
                !is_int($between[0]) ||
                !is_int($between[1])
            )
        ) {
            throw new OutOfBoundsException();
        }
        /** @var array{0: int, 1: int} $between */
        return new self(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}
