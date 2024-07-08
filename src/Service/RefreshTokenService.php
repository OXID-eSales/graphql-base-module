<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepository;

/**
 * Token data access service
 */
class RefreshTokenService implements RefreshTokenServiceInterface
{
    public function __construct(
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly ModuleConfiguration $moduleConfiguration
    ) {
    }

    public function createToken(UserInterface $user): string
    {
        $this->removeExpiredTokens($user);
        $this->refreshTokenRepository->canIssueToken(
            $user,
            $this->moduleConfiguration->getUserTokenQuota()
        );

        $token = $this->refreshTokenRepository->getNewRefreshToken(
            userId: $user->id()->val(),
            lifeTime: $this->moduleConfiguration->getRefreshTokenLifeTime()
        );

        return $token->token();
    }

    private function removeExpiredTokens(UserDataType $user): void
    {
        if (!$user->isAnonymous()) {
            $this->refreshTokenRepository->removeExpiredTokens($user);
        }
    }

    //todo: refreshToken method.
}
