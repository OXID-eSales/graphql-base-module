<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use GraphQL\Error\Error;
use TheCodingMachine\GraphQLite\Annotations\Factory;

class IntegerFilter extends AbstractNumberFilter implements FilterInterface
{
    /**
     * @param null|array{0: int, 1: int} $between
     */
    public function __construct(
        private readonly ?int $equals = null,
        private readonly ?int $lessThan = null,
        private readonly ?int $greaterThan = null,
        private readonly ?array $between = null
    ) {
        if (!isset($equals, $lessthan, $greaterThan, $between)) {
            throw new Error('At least one field for type IntegerFilter must be provided');
        }
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
     * @return null|array{0: int, 1: int}
     */
    public function between(): ?array
    {
        return $this->between;
    }

    /**
     * @Factory(name="IntegerFilterInput", default=true)
     *
     * @param null|int[] $between
     */
    public static function fromUserInput(
        ?int $equals = null,
        ?int $lessThan = null,
        ?int $greaterThan = null,
        ?array $between = null
    ): self {
        self::checkRangeOfBetween($between);

        /** @var array{0: int, 1: int} $between */
        return new self(
            $equals,
            $lessThan,
            $greaterThan,
            $between
        );
    }
}
