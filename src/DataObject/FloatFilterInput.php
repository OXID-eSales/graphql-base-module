<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

class FloatFilterInput
{
    /** @var ?float */
    private $equals;

    /** @var ?float */
    private $lowerThen;

    /** @var ?float */
    private $greaterThen;

    /** @var array{0: float, 1: float}|null */
    private $between;

    /**
     * @param array{0: float, 1: float}|null $between
     */
    public function __construct(
        ?float $equals = null,
        ?float $lowerThen = null,
        ?float $greaterThen = null,
        ?array $between = null
    ) {
        $this->equals      = $equals;
        $this->lowerThen   = $lowerThen;
        $this->greaterThen = $greaterThen;
        $this->between     = $between;
    }

    public function equals(): ?float
    {
        return $this->equals;
    }

    public function lowerThen(): ?float
    {
        return $this->lowerThen;
    }

    public function greaterThen(): ?float
    {
        return $this->greaterThen;
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    public function between(): ?array
    {
        return $this->between;
    }
}
