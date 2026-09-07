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
        string $message,
        private readonly string $value = ''
    ) {
        parent::__construct($code, $message);
    }

    /**
     * @Field()
     */
    public function value(): string
    {
        return $this->value;
    }

    public static function fromCode(string $code, string $value): self
    {
        return new self($code, self::messages()[$code], $value);
    }

    /** @return array<string, string> */
    private static function messages(): array
    {
        return [
            self::FINGERPRINT => 'The fingerprint validation failed.',
            self::REFRESH_TOKEN => 'The provided refresh token is invalid.',
        ];
    }
}
