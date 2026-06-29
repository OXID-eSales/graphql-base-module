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
final class AuthorizationError implements ErrorInterface
{
    public const UNAUTHORIZED_DELETE_TOKEN = 'oegqlb.authorized.delete_token';

    private const MESSAGES = [
        self::UNAUTHORIZED_DELETE_TOKEN => 'You are not authorized to delete this token.',
    ];

    public function __construct(
        private readonly string $code,
        private readonly string $message
    ) {
    }

    public static function fromCode(string $code): self
    {
        return new self($code, self::MESSAGES[$code]);
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
}
