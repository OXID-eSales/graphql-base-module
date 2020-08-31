<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;

final class PermissionProvider implements PermissionProviderInterface
{
    public function getPermissions(): array
    {
        return [
            'oxidadmin' => [
                'SEE_BASKET',
            ],
        ];
    }
}
