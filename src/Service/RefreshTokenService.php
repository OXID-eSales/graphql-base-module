<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\EshopCommunity\Core\Registry;
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

    public function refreshToken(string $refreshToken, string $fingerprintHash): string
    {
        $this->fingerprintService->validateFingerprintHashToCookie($fingerprintHash);

        $user = $this->refreshTokenRepository->getTokenUser($refreshToken);
        $newToken = $this->tokenService->createTokenForUser($user);

        return $newToken->toString();
    }

    public function refreshTokenCookie(UnencryptedToken $token): UnencryptedToken
    {
        $expTime = $token->claims()->get('exp')->getTimestamp();
        if ($expTime > time()) {
            return $token;
        }

        $refreshToken = (string) Registry::getUtilsServer()->getOxCookie('oxapi_refresh');
        $user = $this->refreshTokenRepository->getTokenUser($refreshToken);
        $newToken = $this->tokenService->createTokenForUser($user);
        $newExpTime = $newToken->claims()->get('exp')->getTimestamp();
        Registry::getUtilsServer()->setOxCookie('oxapi_jwt', $newToken->toString(), $newExpTime,null, null, false);

        return $newToken;
    }
}
