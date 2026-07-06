<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;

interface TokenPayloadInterface extends PayloadInterface
{
    public function token(): ?string;
}
