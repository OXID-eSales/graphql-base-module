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
final class AuthenticationError implements ErrorInterface
{
    public const TOKEN_QUOTA_EXCEEDED = 'oegqlb.authentication.token_quota_exceeded';

    private const MESSAGES = [
        self::TOKEN_QUOTA_EXCEEDED => 'The token quota for this user has been exceeded.',
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
