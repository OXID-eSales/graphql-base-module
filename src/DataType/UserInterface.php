<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use TheCodingMachine\GraphQLite\Types\ID;

interface UserInterface
{
    public function email(): string;

    public function id(): ID;

    public function isAnonymous(): bool;
}
