<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyInfrastructure;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

class Authentication implements AuthenticationServiceInterface
{
    public function __construct(
        private readonly LegacyInfrastructure $legacyInfrastructure,
        private Token $tokenService
    ) {
    }

    /**
     * @throws InvalidToken
     */
    public function isLogged(): bool
    {
        if (!$this->tokenService->getToken() || $this->getUser()->isAnonymous()) {
            return false;
        }

        return true;
    }

    public function getUser(): User
    {
        return new User(
            $this->legacyInfrastructure->getUserModel($this->tokenService->getTokenClaim(Token::CLAIM_USERID)),
            $this->tokenService->getTokenClaim(Token::CLAIM_USER_ANONYMOUS, false)
        );
    }
}
