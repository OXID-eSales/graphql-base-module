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
    /** @var LegacyInfrastructure */
    private $legacyInfrastructure;

    /** @var Token */
    private $tokenService;

    public function __construct(
        LegacyInfrastructure $legacyService,
        Token                $tokenService
    ) {
        $this->legacyInfrastructure = $legacyService;
        $this->tokenService         = $tokenService;
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
        $this->validateNotEmptyToken();

        return new User(
            $this->legacyInfrastructure->getUserModel($this->tokenService->getTokenClaim(Token::CLAIM_USERID)),
            $this->tokenService->getTokenClaim(Token::CLAIM_USER_ANONYMOUS, false)
        );
    }

    /**
     * @throws InvalidToken
     */
    protected function validateNotEmptyToken(): void
    {
        if ($token = $this->tokenService->getToken()) {
            $this->tokenService->validateToken($token);
        }
    }
}
