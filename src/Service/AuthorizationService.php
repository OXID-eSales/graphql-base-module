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

    /** @var array<string, string> */
    private $permissions = [];

    public function __construct(
        iterable $permissionProviders
    ) {
        /** @var $permissionProvider \OxidEsales\GraphQL\Framework\PermissionProviderInterface */
        foreach ($permissionProviders as $permissionProvider) {
            $permissions = $permissionProvider->getPermissions();
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
        return false;
    }
}
