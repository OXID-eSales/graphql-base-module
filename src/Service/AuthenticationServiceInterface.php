<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\Token;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface as GraphQLiteAuthenticationServiceInterface;

/**
 * @deprecated no need for a special interface
 */
interface AuthenticationServiceInterface extends GraphQLiteAuthenticationServiceInterface
{
    public function setToken(?Token $token = null): void;

    public function createToken(string $username, string $password): Token;
}
