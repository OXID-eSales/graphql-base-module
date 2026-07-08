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
    public const CREDENTIALS = 'oegqlb.validation.credentials';
    public const FINGERPRINT = 'oegqlb.validation.fingerprint';
    public const REFRESH_TOKEN = 'oegqlb.validation.refresh_token';

    /** @inheritDoc */
    protected static function messages(): array
    {
        return [
            self::CREDENTIALS => 'The provided credentials are invalid.',
            self::FINGERPRINT => 'The fingerprint validation failed.',
            self::REFRESH_TOKEN => 'The provided refresh token is invalid.',
        ];
    }
}
