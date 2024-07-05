<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use DateTimeImmutable;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepository;

/**
 * Token data access service
 */
class RefreshTokenService implements RefreshTokenServiceInterface
{
    public function __construct(
        private readonly RefreshTokenRepository $tokenRepository,
        private readonly ModuleConfiguration $moduleConfiguration
    ) {
    }

    public function createToken(UserDataType $user): string
    {
        $this->removeExpiredTokens($user);
        $this->tokenRepository->canIssueToken(
            $user,
            $this->moduleConfiguration->getUserTokenQuota()
        );

        $time = new DateTimeImmutable('now');
        $expire = new DateTimeImmutable($this->moduleConfiguration->getRefreshTokenLifeTime());

        $token = substr(bin2hex(random_bytes(128)), 0, 255);

        $this->tokenRepository->registerToken($token, $time, $expire, $user);

        return $token;
    }

    private function removeExpiredTokens(UserDataType $user): void
    {
        if (!$user->isAnonymous()) {
            $this->tokenRepository->removeExpiredTokens($user);
        }
    }
}
