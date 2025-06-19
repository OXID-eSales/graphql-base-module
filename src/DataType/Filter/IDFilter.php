<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

use function strtoupper;

class IDFilter implements FilterInterface
{
    protected ?string $from = null;

    public function __construct(private readonly ID $equals)
    {
    }

    public function equals(): ID
    {
        return $this->equals;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $table = $this->getFrom();
        if (empty($table)) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }

        $builder->andWhere(sprintf('%s.%s = :%s', $table, strtoupper($field), $field))
            ->setParameter($field, $this->equals);
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
