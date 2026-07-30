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
class NotCreatedError extends AbstractError
{
    public function __construct(
        string $code,
        string $message,
        private readonly string $identifier,
    ) {
        parent::__construct($code, $message);
    }

    /** @inheritDoc */
    protected static function messages(): array
    {
        return [];
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
