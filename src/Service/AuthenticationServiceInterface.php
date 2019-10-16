<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use Lcobucci\JWT\Token;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface as GraphQLiteAuthenticationServiceInterface;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface as GraphQLiteAuthorizationServiceInterface;

interface AuthenticationServiceInterface extends GraphQLiteAuthenticationServiceInterface, GraphQLiteAuthorizationServiceInterface
{
    public function createToken(string $username, string $password, int $shopid = null): Token;
}
