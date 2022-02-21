<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

use GraphQL\Error\Error as GraphQLError;
use Throwable;

abstract class Error extends GraphQLError
{
    /** @var string */
    protected $category;

    /** @var int */
    protected $code;

    /**
     * @param array<string, mixed> $extensions
     */
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null, string $category = 'Exception', array $extensions = [])
    {
        parent::__construct(
            $message,
            null,
            null,
            [],
            null,
            $previous,
            $extensions
        );
        $this->category = $category;
        $this->code     = $code;
    }

    /**
     * Returns true when exception message is safe to be displayed to a client.
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * Returns string describing a category of the error.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     */
    public function getCategory(): string
    {
        return $this->category;
    }
}
