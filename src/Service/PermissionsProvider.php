<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

class PermissionsProvider implements PermissionsProviderInterface
{
    protected $permissions = [];

    public function addPermission($group, $permission)
    {
        if (! array_key_exists($group, $this->permissions)) {
            $this->permissions[$group] = [];
        }
        $this->permissions[$group][] = $permission;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

}
