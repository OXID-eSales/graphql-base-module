<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Error\Error;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class PaginationFilter
{
    /** @var int */
    private $offset = 0;

    /** @var ?int */
    private $limit;

    /**
     * PaginationFilter constructor.
     *
     * @throws Error
     */
    public function __construct(
        int $offset = 0,
        ?int $limit = null
    ) {
        if (
            $offset < 0 ||
            ($limit !== null && $limit <= 0)
        ) {
            throw new Error('PaginationFilter fields must be positive.');
        }

        $this->offset = $offset;
        $this->limit  = $limit;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function addPaginationToQuery(QueryBuilder $builder): void
    {
        $builder->setFirstResult($this->offset);

        if ($this->limit !== null && $this->limit > 0) {
            $builder->setMaxResults($this->limit);
        }
    }

    /** @Factory(name="PaginationFilterInput") */
    public static function fromUserInput(
        int $offset = 0,
        ?int $limit = null
    ): self {
        return new self(
            $offset,
            $limit
        );
    }
}
