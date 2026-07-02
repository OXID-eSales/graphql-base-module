<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;

class LoginExceptionConverter implements LoginExceptionConverterInterface
{
    public function __construct(
        private readonly Token $tokenService,
        private readonly LoginServiceInterface $loginService,
    ) {
    }

    public function createToken(?string $username, ?string $password): UnencryptedToken|ErrorInterface
    {
        try {
            return $this->tokenService->createToken($username, $password);
        } catch (InvalidLogin) {
            return ValidationError::fromCode(ValidationError::INVALID_CREDENTIALS);
        } catch (TokenQuota) {
            return AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED);
        }
    }

    public function login(?string $username, ?string $password): LoginInterface|ErrorInterface
    {
        try {
            return $this->loginService->login($username, $password);
        } catch (InvalidLogin) {
            return ValidationError::fromCode(ValidationError::INVALID_CREDENTIALS);
        } catch (TokenQuota) {
            return AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED);
        }
    }
}
