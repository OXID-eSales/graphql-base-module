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
    public const NOT_FOUND = 'oegqlb.not_found';
    public const NOT_FOUND_TOKEN = 'oegqlb.not_found.token';
    public const NOT_FOUND_USER = 'oegqlb.not_found.user';

    public function __construct(
        string $code,
        string $message,
        private readonly string $identifier
    ) {
        parent::__construct($code, $message);
    }

    /** @inheritDoc */
    protected static function messages(): array
    {
        return [
            self::NOT_FOUND => 'The requested resource was not found.',
            self::NOT_FOUND_TOKEN => 'The requested token was not found.',
            self::NOT_FOUND_USER => 'The requested user was not found.',
        ];
    }

    public static function fromCode(string $code, string $identifier = ''): static
    {
        return new static($code, static::messages()[$code], $identifier);
    }

    /**
     * @Field()
     */
    public function identifier(): string
    {
        return $this->identifier;
    }
}
