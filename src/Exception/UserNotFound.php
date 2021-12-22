<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

use function sprintf;

final class UserNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('User was not found by id: %s', $id));
    }
}
