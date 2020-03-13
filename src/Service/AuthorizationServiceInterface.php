<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\Token;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface as GraphQLiteAuthorizationServiceInterface;

/**
 * @deprecated
 */
interface AuthorizationServiceInterface extends GraphQLiteAuthorizationServiceInterface
{
    public function setToken(?Token $token = null): void;
}
