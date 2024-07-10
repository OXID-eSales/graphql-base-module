<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\UserInterface;

/**
 * User login service
 */
interface LoginServiceInterface
{
    public function login(?string $userName, ?string $password): UserInterface;
}
