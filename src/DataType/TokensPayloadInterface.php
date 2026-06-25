<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;

interface TokensPayloadInterface extends PayloadInterface
{
    /** @return Token[] */
    public function tokens(): array;
}

