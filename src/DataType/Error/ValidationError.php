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
final class ValidationError extends AbstractError
{
    public const INVALID_CREDENTIALS = 'oegqlb.validation.invalid_credentials';
    public const INVALID_FINGERPRINT = 'oegqlb.validation.invalid_fingerprint';
    public const INVALID_REFRESH_TOKEN = 'oegqlb.validation.invalid_refresh_token';

    /** @inheritDoc */
    protected static function messages(): array
    {
        return [
            self::INVALID_CREDENTIALS => 'The provided credentials are invalid.',
            self::INVALID_FINGERPRINT => 'The fingerprint validation failed.',
            self::INVALID_REFRESH_TOKEN => 'The provided refresh token is invalid.',
        ];
    }
}
