<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;

interface LoginPayloadInterface extends PayloadInterface
{
    public function login(): ?LoginInterface;
}
