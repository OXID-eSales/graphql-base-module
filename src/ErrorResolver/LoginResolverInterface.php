<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\ErrorResolver;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;

interface LoginResolverInterface
{
    public function createToken(?string $username, ?string $password): UnencryptedToken|ErrorInterface;

    public function login(?string $username, ?string $password): LoginInterface|ErrorInterface;
}
