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
    public const TOKEN_QUOTA_EXCEEDED = 'oegqlb.authentication.token_quota_exceeded';

    /** @inheritDoc */
    protected static function messages(): array
    {
        return [
            self::TOKEN_QUOTA_EXCEEDED => 'The token quota for this user has been exceeded.',
        ];
    }
}
