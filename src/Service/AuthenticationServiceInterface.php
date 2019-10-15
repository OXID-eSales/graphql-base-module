<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

/*
use OxidEsales\GraphQL\DataObject\Token;
use OxidEsales\GraphQL\DataObject\TokenRequest;
 */

use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface as GraphQLiteAuthenticationServiceInterface;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface as GraphQLiteAuthorizationServiceInterface;

#interface AuthenticationServiceInterface
interface AuthenticationServiceInterface extends GraphQLiteAuthenticationServiceInterface, GraphQLiteAuthorizationServiceInterface
{
    /**
     * @param TokenRequest $tokenRequest
     *
     * @return Token
     */
    /*
        public function getToken(TokenRequest $tokenRequest): Token;
     */
}
