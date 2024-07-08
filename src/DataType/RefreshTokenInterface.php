<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use DateTimeInterface;
use TheCodingMachine\GraphQLite\Types\ID;

interface RefreshTokenInterface
{
    public function id(): ID;

    public function token(): string;

    public function createdAt(): ?DateTimeInterface;

    public function expiresAt(): ?DateTimeInterface;

    public function customerId(): ID;

    public function shopId(): ID;
}
