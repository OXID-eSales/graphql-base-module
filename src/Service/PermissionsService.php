<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\MissingPermissionException;

class PermissionsService implements PermissionsServiceInterface
{
    private $permissions = [];

    public function addPermission($group, $permission)
    {
        if (! array_key_exists($group, $this->permissions)) {
            $this->permissions[$group] = [];
        }
        $this->permissions[$group][] = $permission;
    }

    /**
     * It is possible to either one or several permissions
     * as an array. If there are several permissions, they
     * are or-ed.
     *
     * @throws MissingPermissionException
     *
     * @param Token|null $token
     * @param string|array $permissions
     */
    public function checkPermission($token, $permissions)
    {
        if (! is_array($permissions))
        {
            $permissions = [$permissions];
        }
        if (! $token) {
            throw new MissingPermissionException("User without authentication does not have permission " .
                                                 $this->formatPermissions($permissions));
        }
        $group = $token->getUserGroup();
        if ($group == 'developer') {
            return;
        }
        if (! array_key_exists($group, $this->permissions))
        {
            throw new MissingPermissionException("User " . $token->getSubject() . " has no permissions at all.");
        }

        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions[$group])) {
                return;
            }
        }

        throw new MissingPermissionException("User " . $token->getSubject() . " does not have permission " .
                                             $this->formatPermissions($permissions));

    }

    private function formatPermissions(array $permissions)
    {
        $joiner = '';
        $result = '';
        foreach ($permissions as $permission){
            $result .= "$joiner'$permission'";
            $joiner = ' or ';
        }
    }
}
