<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use DateTimeImmutable;
use OxidEsales\GraphQL\Base\DataType\Login;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshToken as TokenInfrastructure;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * Token data access service
 */
class RefreshToken
{
    public function __construct(
        private Legacy $legacyInfrastructure,
        private TokenInfrastructure $tokenInfrastructure,
        private AccessToken $accessTokenService
    ) {
    }

    public function login(?string $username = null, ?string $password = null): Login
    {
        $refreshToken = $this->createToken($username, $password);
        $accessToken = $this->accessTokenService->createToken($refreshToken);

        return new Login($refreshToken, $accessToken->toString());
    }

    public function createToken(?string $username = null, ?string $password = null): string
    {
        /** @var UserDataType $user */
        $user = $this->legacyInfrastructure->login($username, $password);
        $this->removeExpiredTokens($user);

        $time = new DateTimeImmutable('now');
        $expire = new DateTimeImmutable('1month'); // TODO: should be configurable

        $token = substr(bin2hex(random_bytes(128)), 0, 255);

        $this->tokenInfrastructure->registerToken($token, $time, $expire, $user);

        return $token;
    }

    public function deleteToken(ID $tokenId): void
    {
        $tokenId = (string)$tokenId;

        if ($this->tokenInfrastructure->isTokenRegistered($tokenId)) {
            $this->tokenInfrastructure->tokenDelete(null, $tokenId);
        } else {
            throw InvalidToken::unknownToken();
        }
    }

    public function deleteUserToken(UserDataType $user, ID $tokenId): void
    {
        if ($this->tokenInfrastructure->userHasToken($user, (string)$tokenId)) {
            $this->tokenInfrastructure->tokenDelete($user, (string)$tokenId);
        } else {
            throw InvalidToken::unknownToken();
        }
    }

    private function removeExpiredTokens(UserDataType $user): void
    {
        if (!$user->isAnonymous()) {
            $this->tokenInfrastructure->removeExpiredTokens($user);
        }
    }
}
