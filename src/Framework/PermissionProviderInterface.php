<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

interface PermissionProviderInterface
{
    /**
     * @return array<string, array<string>>
     */
    public function getPermissions(): array;
}
