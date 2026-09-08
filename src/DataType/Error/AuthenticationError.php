<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Error;

use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class AuthenticationError extends AbstractError
{
    public const CREDENTIALS_INCORRECT = 'oegqlb.authentication.credentials_incorrect';
    public const TOKEN_QUOTA_EXCEEDED = 'oegqlb.authentication.token_quota_exceeded';

    public function __construct(string $code)
    {
        parent::__construct($code, $this->messages()[$code]);
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            self::CREDENTIALS_INCORRECT => 'The provided credentials are invalid.',
            self::TOKEN_QUOTA_EXCEEDED => 'The token quota for this user has been exceeded.',
        ];
    }
}
