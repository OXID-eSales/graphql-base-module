<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Error;

use TheCodingMachine\GraphQLite\Annotations\Field;

abstract class AbstractPayload implements PayloadInterface
{
    /**
     * @param ErrorInterface[] $userErrors
     */
    public function __construct(
        private readonly array $userErrors = []
    ) {
    }

    /**
     * @return ErrorInterface[]
     */
    #[Field]
    public function userErrors(): array
    {
        return $this->userErrors;
    }
}
