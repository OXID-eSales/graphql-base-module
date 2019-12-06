<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

class IntegerFilterInput
{
    /** @var ?int */
    private $equals;

    /** @var ?int */
    private $lowerThen;

    /** @var ?int */
    private $greaterThen;

    /** @var array{0: int, 1: int}|null */
    private $between;

    /**
     * @param array{0: int, 1: int}|null $between
     */
    public function __construct(
        ?int $equals = null,
        ?int $lowerThen = null,
        ?int $greaterThen = null,
        ?array $between = null
    ) {
        $this->equals      = $equals;
        $this->lowerThen   = $lowerThen;
        $this->greaterThen = $greaterThen;
        $this->between     = $between;
    }

    public function equals(): ?int
    {
        return $this->equals;
    }

    public function lowerThen(): ?int
    {
        return $this->lowerThen;
    }

    public function greaterThen(): ?int
    {
        return $this->greaterThen;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    public function between(): ?array
    {
        return $this->between;
    }
}
