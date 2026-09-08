<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Error;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ValidationError extends AbstractError
{
    public const FINGERPRINT = 'oegqlb.validation.fingerprint';
    public const REFRESH_TOKEN = 'oegqlb.validation.refresh_token';

    public function __construct(
        string $code,
        private readonly string $value = ''
    ) {
        parent::__construct($code, $this->messages()[$code]);
    }

    /**
     * @Field()
     */
    public function value(): string
    {
        return $this->value;
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            self::FINGERPRINT => 'The fingerprint validation failed.',
            self::REFRESH_TOKEN => 'The provided refresh token is invalid.',
        ];
    }
}
