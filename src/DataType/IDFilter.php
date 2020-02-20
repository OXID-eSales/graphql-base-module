<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

use function strtoupper;

class IDFilter implements FilterInterface
{
    /** @var ID */
    private $equals;

    public function __construct(
        ID $equals
    ) {
        $this->equals = $equals;
    }

    public function equals(): ID
    {
        return $this->equals;
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $builder->andWhere(strtoupper($field) . ' = :' . $field)
                ->setParameter(':' . $field, $this->equals);
    }

    /**
     * @Factory(name="IDFilterInput")
     */
    public static function fromUserInput(
        ID $equals
    ): self {
        return new self(
            $equals
        );
    }
}
