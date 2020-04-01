<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Service;

/**
 * @deprecated no need for an interface, use KeyRegistry
 */
interface KeyRegistryInterface
{
    public function generateSignatureKey(): string;

    public function getSignatureKey(): string;
}
