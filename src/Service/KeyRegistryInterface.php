<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

/**
 * @package OxidEsales\GraphQL\Service
 */
interface KeyRegistryInterface
{
    public function generateSignatureKey(): string;

    public function getSignatureKey(): string;
}
