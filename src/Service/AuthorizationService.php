<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use Lcobucci\JWT\Token;

class AuthorizationService implements AuthorizationServiceInterface
{
    /** @var Token */
    private $token = null;

    /** @var array<string, array<string>> */
    private $permissions = [];

    public function __construct(
        iterable $permissionProviders
    ) {
        /** @var $permissionProvider \OxidEsales\GraphQL\Framework\PermissionProviderInterface */
        foreach ($permissionProviders as $permissionProvider) {
            $this->permissions = array_merge_recursive(
                $this->permissions,
                $permissionProvider->getPermissions()
            );
        }
    }

    /**
     * TODO: validate token!!
     */
    public function setToken(?Token $token = null)
    {
        $this->token = $token;
    }
 
    public function isAllowed(string $right): bool
    {
        if ($this->token === null) {
            return false;
        }

        if ($this->isDeveloperModuleRequest()) {
            return true;
        }

        $group = $this->token->getClaim(AuthenticationService::CLAIM_GROUP);
        if (!isset($this->permissions[$group])) {
            return false;
        }
        if (array_search($right, $this->permissions[$group], true) === false) {
            return false;
        }
        return true;
    }

    /**
     * Checks if the user name is 'developer' and the developer module is active
     *
     * The check if the module is active is done using permissions. The module
     * sets the permission 'all' for the group 'developers'. So if this permission
     * is not set, the module is not active.
     *
     * @return bool
     */
    private function isDeveloperModuleRequest()
    {
        $username = $this->token->getClaim(AuthenticationService::CLAIM_USERNAME);
        if ($username === 'developer' and isset($this->permissions['developer'])) {
            if (array_search('all', $this->permissions['developer'], true) !== false) {
                return true;
            }
        }
        return false;
    }
}
