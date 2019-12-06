<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

class BoolFilterInput
{
    /** @var ?bool */
    private $equals;

    public function __construct(
        ?bool $equals = null
    ) {
        $this->equals      = $equals;
    }

    public function equals(): ?bool
    {
        return $this->equals;
    }
}
