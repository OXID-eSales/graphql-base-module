<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;

class AnonymousUserData implements UserDataInterface
{
    public function getUserId(): string
    {
        return Legacy::createUniqueIdentifier();
    }

    /**
     * @return string[]
     */
    public function getUserGroupIds(): array
    {
        return ['oxidanonymous'];
    }
}
