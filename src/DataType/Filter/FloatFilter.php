<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Filter;

use GraphQL\Error\Error;
use TheCodingMachine\GraphQLite\Annotations\Factory;

class FloatFilter extends AbstractNumberFilter implements FilterInterface
{
    /**
     * @param null|array{0: float, 1: float} $between
     */
    public function __construct(
        private readonly ?float $equals = null,
        private readonly ?float $lessThan = null,
        private readonly ?float $greaterThan = null,
        private readonly ?array $between = null
    ) {
        if (!isset($equals, $lessthan, $greaterThan, $between)) {
            throw new Error('At least one field for type FloatFilter must be provided');
        }
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
     * @return null|array{0: float, 1: float}
     */
    public function between(): ?array
    {
        return $this->between;
    }

    /**
     * @Factory(name="FloatFilterInput", default=true)
     *
     * @param null|float[] $between
     */
    public static function fromUserInput(
        ?float $equals = null,
        ?float $lessThan = null,
        ?float $greaterThan = null,
        ?array $between = null
    ): self {
        self::checkRangeOfBetween($between);

        /** @var array{0: float, 1: float} $between */
        return new self(
            $equals,
            $lessThan,
            $greaterThan,
            $between
        );
    }
}
