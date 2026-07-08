<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\LoginPayload;
use OxidEsales\GraphQL\Base\DataType\Payload\LoginPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenPayload;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenPayloadInterface;
use OxidEsales\GraphQL\Base\ExceptionConverter\LoginExceptionConverterInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    public function __construct(
        private readonly LoginExceptionConverterInterface $loginExceptionConverter,
    ) {
    }

    /**
     * Query of Base Module.
     * Retrieve a JWT for authentication of further requests
     */
    #[Query(outputType: 'TokenPayload')]
    public function token(?string $username = null, ?string $password = null): TokenPayloadInterface
    {
        $result = $this->loginExceptionConverter->createToken($username, $password);

        if ($result instanceof ErrorInterface) {
            return new TokenPayload(null, [$result]);
        }

        return new TokenPayload($result->toString());
    }

    /**
     * Query of Base Module.
     * Retrieve a refresh token and access token
     */
    #[Query(outputType: 'LoginPayload')]
    public function login(?string $username = null, ?string $password = null): LoginPayloadInterface
    {
        $result = $this->loginExceptionConverter->login($username, $password);

        if ($result instanceof ErrorInterface) {
            return new LoginPayload(null, [$result]);
        }

        return new LoginPayload($result);
    }
}
