<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use OxidEsales\GraphQL\Framework\RequestReaderInterface;

class AuthorizationService implements AuthorizationServiceInterface
{
    const USER_GROUP_ANONMYOUS = 'anonymous';
    const USER_GROUP_CUSTOMER  = 'customer';
    const USER_GROUP_ADMIN     = 'admin';
    const USER_GROUP_SHOPADMIN = 'shopadmin';

    /** @var RequestReaderInterface */
    private $requestReader;

    /** @var PermissionProvidersInterface[] */
    private $permissionProviders;

    public function __construct(
        iterable $permissionProviders,
        RequestReaderInterface $requestReader
    ) {
        $this->permissionProviders = $permissionProviders;
        $this->requestReader = $requestReader;
    }
 
    public function isAllowed(string $right): bool
    {
        return false;
    }
}
