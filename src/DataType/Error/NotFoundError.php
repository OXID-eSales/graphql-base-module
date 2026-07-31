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
 * @phpstan-consistent-constructor
 */
class NotFoundError extends AbstractError
{
    public const TOKEN = 'oegqlb.not_found.token';
    public const USER = 'oegqlb.not_found.user';

    public function __construct(
        string $code,
        string $message,
        private readonly string $identifier
    ) {
        parent::__construct($code, $message);
    }

    /**
     * @Field()
     */
    public function identifier(): string
    {
        return $this->identifier;
    }

    public static function fromCode(string $code, string $identifier): self
    {
        return new self($code, self::messages()[$code], $identifier);
    }

    /** @return array<string, string> */
    private static function messages(): array
    {
        return [
            self::TOKEN => 'The token was not found.',
            self::USER => 'The user was not found.',
        ];
    }
}
