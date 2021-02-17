<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

interface UserDataInterface
{
    public function getUserId(): string;

    /**
     * @return string[]
     */
    public function getUserGroupIds(): array;
}
