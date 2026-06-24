<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataType\Error;

interface PayloadInterface
{
    /**
     * @return ErrorInterface[]
     */
    public function userErrors(): array;
}
