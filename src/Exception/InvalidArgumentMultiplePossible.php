<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class InvalidArgumentMultiplePossible extends Error
{
    public function __construct(string $field, array $validValues, string $invalidValue)
    {
        $message = '"' . $field . '" is only allowed to be one of "' .
                implode(', ', $validValues) . '", was "' . $invalidValue . '"';

        parent::__construct($message);
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }
}
