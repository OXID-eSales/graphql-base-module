<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\LoginPayload;
use OxidEsales\GraphQL\Base\DataType\LoginPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\TokenPayload;
use OxidEsales\GraphQL\Base\DataType\TokenPayloadInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Service\LoginServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    public function __construct(
        protected Token $tokenService,
        protected LoginServiceInterface $loginService,
    ) {
    }

    /**
     * Query of Base Module.
     * Retrieve a JWT for authentication of further requests
     *
     * @Query(outputType="TokenPayload")
     */
    public function token(?string $username = null, ?string $password = null): TokenPayloadInterface
    {
        try {
            return new TokenPayload($this->tokenService->createToken($username, $password)->toString());
        } catch (InvalidLogin) {
            return new TokenPayload(null, [ValidationError::fromCode(ValidationError::INVALID_CREDENTIALS)]);
        } catch (TokenQuota) {
            return new TokenPayload(null, [AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED)]);
        }
    }

    /**
     * Query of Base Module.
     * Retrieve a refresh token and access token
     *
     * @Query(outputType="LoginPayload")
     */
    public function login(?string $username = null, ?string $password = null): LoginPayloadInterface
    {
        try {
            return new LoginPayload($this->loginService->login($username, $password));
        } catch (InvalidLogin) {
            return new LoginPayload(null, [ValidationError::fromCode(ValidationError::INVALID_CREDENTIALS)]);
        } catch (TokenQuota) {
            return new LoginPayload(null, [AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED)]);
        }
    }
}
