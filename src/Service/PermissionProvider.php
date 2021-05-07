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
        return [
            'oxidadmin' => [
                'TODO',
                'FOOBAR',
                'WAHOO',
                'PRINT_STRING'
            ],
            'oxidnewcustomer' => [
                'PRINT_DATE'
            ],
            'malladmin' => [
                'GAGA',
                'FOOBAR',
            ],
        ];
    }
}
