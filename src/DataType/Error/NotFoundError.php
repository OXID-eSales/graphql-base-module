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
final class NotFoundError implements ErrorInterface
{
    public const NOT_FOUND = 'oegqlb.not_found';
    public const NOT_FOUND_TOKEN = 'oegqlb.not_found.token';
    public const NOT_FOUND_USER = 'oegqlb.not_found.user';

    private const MESSAGES = [
        self::NOT_FOUND => 'The requested resource was not found.',
        self::NOT_FOUND_TOKEN => 'The requested token was not found.',
        self::NOT_FOUND_USER => 'The requested user was not found.',
    ];

    public function __construct(
        private readonly string $code,
        private readonly string $message,
        private readonly string $identifier
    ) {
    }

    public static function fromCode(string $code, string $identifier): self
    {
        return new self($code, self::MESSAGES[$code], $identifier);
    }

    /**
     * @Field()
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * @Field()
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @Field()
     */
    public function identifier(): string
    {
        return $this->identifier;
    }
}
