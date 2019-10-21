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
        $group = $this->token->getClaim(AuthenticationService::CLAIM_GROUP);
        if (!isset($this->permissions[$group])) {
            return false;
        }
        if (array_search($right, $this->permissions[$group], true) === false) {
            return false;
        }
        return true;
    }
}
