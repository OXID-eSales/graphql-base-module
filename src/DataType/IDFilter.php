<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
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
        $from = $builder->getQueryPart('from');

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }
        $table = $from[0]['alias'] ?? $from[0]['table'];

        $builder->andWhere(sprintf('%s.%s = :%s', $table, strtoupper($field), $field))
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
