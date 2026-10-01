<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataType\Payload;

interface BooleanPayloadInterface extends PayloadInterface
{
    public function success(): ?bool;
}
