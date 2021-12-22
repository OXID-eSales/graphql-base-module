<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Sorting;

use TheCodingMachine\GraphQLite\Annotations\Factory;

final class TokenSorting extends Sorting
{
    /**
     * @Factory(name="TokenSorting",default=true)
     *
     * Tokens will be sorted by their expiration date ('expires_at' column).
     */
    public static function fromUserInput(
        string $expiresAt = self::SORTING_ASC
    ): self {
        return new self([
            'expires_at'    => $expiresAt,
        ]);
    }
}
