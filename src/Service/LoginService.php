<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;

/**
 * User login service
 */
class LoginService implements LoginServiceInterface
{
    public function __construct(
        private readonly Legacy $legacyInfrastructure,
    ) {
    }

    public function login(string $userName, string $password): UserInterface
    {
        return $this->legacyInfrastructure->login($userName, $password);
    }
}
