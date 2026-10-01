<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Error;

use InvalidArgumentException;
use TheCodingMachine\GraphQLite\Annotations\Field;

abstract class AbstractError implements ErrorInterface
{
    private readonly string $message;

    public function __construct(
        private readonly string $code,
        ?string $message = null,
    ) {
        $messages = $this->messages();

        if ($message === null && !array_key_exists($code, $messages)) {
            throw new InvalidArgumentException('The error-code "' . $code . '" is unknown.');
        }

        $this->message = $message ?? $messages[$code];
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /** @return array<string, string> */
    abstract protected function messages(): array;
}
