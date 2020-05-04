<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;
use InvalidArgumentException;
use OutOfBoundsException;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function strtoupper;

/**
 * moar moar moar
 */
class DateFilter implements FilterInterface
{
    public const SQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** @var ?DateTimeInterface */
    private $equals;

    /** @var array{0: DateTimeInterface, 1: DateTimeInterface}|null */
    private $between;

    /**
     * @param array{0: DateTimeInterface, 1: DateTimeInterface}|null $between
     */
    public function __construct(
        ?DateTimeInterface $equals = null,
        ?array $between = null
    ) {
        if (
            $equals === null &&
            $between === null
        ) {
            throw new Error('At least one field for type DateFilterInput must be provided');
        }
        $this->equals      = $equals;
        $this->between     = $between;
    }

    public function equals(): ?DateTimeInterface
    {
        return $this->equals;
    }

    /**
     * @return array{0: DateTimeInterface, 1: DateTimeInterface}|null
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
            $builder->andWhere($table . '.' . strtoupper($field) . ' = :' . $field . '_eq')
                    ->setParameter(':' . $field . '_eq', $this->equals->format(self::SQL_DATETIME_FORMAT));
            // if equals is set, then no other conditions may apply
            return;
        }

        if ($this->between) {
            $where = sprintf('%s.%s BETWEEN :%s_lower AND :%s_upper', $table, strtoupper($field), $field, $field);
            $builder->andWhere($where)
                    ->setParameter(':' . $field . '_lower', $this->between[0]->format(self::SQL_DATETIME_FORMAT))
                    ->setParameter(':' . $field . '_upper', $this->between[1]->format(self::SQL_DATETIME_FORMAT));
        }
    }

    /**
     * @Factory(name="DateFilterInput")
     *
     * @param null|string[] $between
     */
    public static function fromUserInput(
        ?string $equals = null,
        ?array $between = null
    ): self {
        if (
            $between !== null && (
                count($between) !== 2 ||
                !is_string($between[0]) ||
                !is_string($between[1])
            )
        ) {
            throw new OutOfBoundsException();
        }
        $zone = new DateTimeZone('UTC');

        if ($equals !== null) {
            $equals = new DateTimeImmutable($equals, $zone);
        }

        if ($between) {
            $between = array_map(
                function ($date) use ($zone) {
                    return new DateTimeImmutable($date, $zone);
                },
                $between
            );
        }
        /** @var array{0: DateTimeInterface, 1: DateTimeInterface} $between */
        return new self(
            $equals,
            $between
        );
    }
}
