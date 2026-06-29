<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;

interface CreationPayloadInterface extends PayloadInterface
{
    public function success(): ?bool;
}
