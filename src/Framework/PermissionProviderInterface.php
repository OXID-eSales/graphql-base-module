<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

interface PermissionProviderInterface
{
    /**
     * @return array<string, array<string>>
     */
    public function getPermissions(): array;
}
