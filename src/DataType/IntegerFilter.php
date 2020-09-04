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
use OutOfBoundsException;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function count;

class IntegerFilter implements FilterInterface
{
    /** @var ?int */
    private $equals;

    /** @var ?int */
    private $lessThan;

    /** @var ?int */
    private $greaterThan;

    /** @var array{0: int, 1: int}|null */
    private $between;

    /**
     * @param array{0: int, 1: int}|null $between
     */
    public function __construct(
        ?int $equals = null,
        ?int $lessThan = null,
        ?int $greaterThan = null,
        ?array $between = null
    ) {
        if (
            $equals === null &&
            $lessThan === null &&
            $greaterThan === null &&
            $between === null
        ) {
            throw new Error('At least one field for type IntegerFilter must be provided');
        }
        $this->equals      = $equals;
        $this->lessThan    = $lessThan;
        $this->greaterThan = $greaterThan;
        $this->between     = $between;
    }

    public function equals(): ?int
    {
        return $this->equals;
    }

    public function lessThan(): ?int
    {
        return $this->lessThan;
    }

    public function greaterThan(): ?int
    {
        return $this->greaterThan;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    public function between(): ?array
    {
        return $this->between;
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

        if ($this->lessThan) {
            $builder->andWhere(sprintf('%s.%s < :%s_lt', $table, strtoupper($field), $field))
                    ->setParameter(':' . $field . '_lt', $this->lessThan);
        }

        if ($this->greaterThan) {
            $builder->andWhere(sprintf('%s.%s > :%s_gt', $table, strtoupper($field), $field))
                    ->setParameter(':' . $field . '_gt', $this->greaterThan);
        }

        if ($this->between) {
            $where = sprintf('%s.%s BETWEEN :%s_less AND :%s_upper', $table, strtoupper($field), $field, $field);
            $builder->andWhere($where)
                    ->setParameter(':' . $field . '_less', $this->between[0])
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
        ?int $lessThan = null,
        ?int $greaterThan = null,
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
            $lessThan,
            $greaterThan,
            $between
        );
    }
}
