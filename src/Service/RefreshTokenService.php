<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepositoryInterface;

/**
 * Token data access service
 */
class RefreshTokenService implements RefreshTokenServiceInterface
{
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly ModuleConfiguration $moduleConfiguration,
        private readonly Token $tokenService,
        private readonly FingerprintServiceInterface $fingerprintService,
    ) {
    }

    public function createRefreshTokenForUser(UserInterface $user): string
    {
        $this->refreshTokenRepository->removeExpiredTokens();

        $token = $this->refreshTokenRepository->getNewRefreshToken(
            userId: (string)$user->id(),
            lifeTime: $this->moduleConfiguration->getRefreshTokenLifeTime()
        );

        return $token->token();
    }

    public function refreshToken(string $refreshToken, string $fingerprintHash): UnencryptedToken
    {
        $this->fingerprintService->validateFingerprintHashToCookie($fingerprintHash);

        $user = $this->refreshTokenRepository->getTokenUser($refreshToken);

        return $this->tokenService->createTokenForUser($user);
    }
}
