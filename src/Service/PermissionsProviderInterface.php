<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

interface PermissionsProviderInterface
{
    public function addPermission($group, $permission);

    public function getPermissions();
}
