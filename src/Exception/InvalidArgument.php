<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class InvalidArgument extends Error
{
    public function __construct(array $validEquals, string $invalidEqual) {
        $message = '"equals" is only allowed to be one of "' .
                implode(', ', $validEquals) . '"  was "' . $invalidEqual . '"';

        parent::__construct($message);
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }
}
