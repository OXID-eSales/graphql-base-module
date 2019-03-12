<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\MissingPermissionException;
use OxidEsales\GraphQl\Utility\AuthConstants;

class PermissionsService  implements PermissionsServiceInterface
{

    const PermissionHeader = 'Missing Permission';

    private $permissions = [];

    private $acceptDeveloperChecks = false;

    public function addPermissionsProvider(PermissionsProvider $provider)
    {
        foreach ($provider->getPermissions() as $group => $permissions) {
            if ($group === AuthConstants::USER_GROUP_DEVELOPER) {
                $this->acceptDeveloperChecks = true;
                continue;
            }
            if (! array_key_exists($group, $this->permissions)) {
                $this->permissions[$group] = [];
            }
            $this->permissions[$group] = array_merge($this->permissions[$group], $permissions);
        }
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
            throw new MissingPermissionException($this::PermissionHeader . ": User without authentication does not have permission " .
                                                 $this->formatPermissions($permissions));
        }
        $group = $token->getUserGroup();
        if ($this->acceptDeveloperChecks && $group == AuthConstants::USER_GROUP_DEVELOPER) {
            return;
        }
        if (! array_key_exists($group, $this->permissions))
        {
            throw new MissingPermissionException($this::PermissionHeader . ": User " . $token->getUserName() . " with group " .
                                                 $token->getUserGroup() . " has no permissions at all.");
        }

        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions[$group])) {
                return;
            }
        }

        throw new MissingPermissionException($this::PermissionHeader . ": User " . $token->getUserName() . " does not have permission " .
                                             $this->formatPermissions($permissions));

    }

    private function formatPermissions(array $permissions)
    {
        $joiner = '';
        $result = '';
        foreach ($permissions as $permission){
            $result .= "$joiner\"$permission\"";
            $joiner = ' or ';
        }
        return $result;
    }
}
