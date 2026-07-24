<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Error;

use TheCodingMachine\GraphQLite\Annotations\Field;

/** @phpstan-consistent-constructor */
abstract class AbstractError implements ErrorInterface
{
    public function __construct(
        private readonly string $code,
        private readonly string $message
    ) {
    }

    /** @return array<string, string> */
    abstract protected static function messages(): array;

    public static function fromCode(string $code): static
    {
        return new static($code, static::messages()[$code]);
    }

    #[Field]
    public function code(): string
    {
        return $this->code;
    }

    #[Field]
    public function message(): string
    {
        return $this->message;
    }
}
