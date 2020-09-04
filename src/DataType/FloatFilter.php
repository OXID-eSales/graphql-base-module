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
use function strtoupper;

class FloatFilter implements FilterInterface
{
    /** @var ?float */
    private $equals;

    /** @var ?float */
    private $lessThan;

    /** @var ?float */
    private $greaterThan;

    /** @var array{0: float, 1: float}|null */
    private $between;

    /**
     * @param array{0: float, 1: float}|null $between
     */
    public function __construct(
        ?float $equals = null,
        ?float $lessThan = null,
        ?float $greaterThan = null,
        ?array $between = null
    ) {
        if (
            $equals === null &&
            $lessThan === null &&
            $greaterThan === null &&
            $between === null
        ) {
            throw new Error('At least one field for type FloatFilter must be provided');
        }
        $this->equals      = $equals;
        $this->lessThan    = $lessThan;
        $this->greaterThan = $greaterThan;
        $this->between     = $between;
    }

    public function equals(): ?float
    {
        return $this->equals;
    }

    public function lessThan(): ?float
    {
        return $this->lessThan;
    }

    public function greaterThan(): ?float
    {
        return $this->greaterThan;
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
     * @Factory(name="FloatFilterInput")
     *
     * @param null|float[] $between
     */
    public static function fromUserInput(
        ?float $equals = null,
        ?float $lessThan = null,
        ?float $greaterThan = null,
        ?array $between = null
    ): self {
        if (
            $between !== null && (
                count($between) !== 2 ||
            !is_float($between[0]) ||
            !is_float($between[1])
            )
        ) {
            throw new OutOfBoundsException();
        }
        /** @var array{0: float, 1: float} $between */
        return new self(
            $equals,
            $lessThan,
            $greaterThan,
            $between
        );
    }
}
