<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;

class PermissionProvider implements PermissionProviderInterface
{
    public function getPermissions(): array
    {
        // TODO: no permissions needed in base module
        return [
            'admin' => [
                'user_get_self'
            ]
        ];
    }
}
