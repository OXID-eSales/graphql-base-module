<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure\Model;

class RefreshTokenModelFactory implements RefreshTokenModelFactoryInterface
{
    public function create(): RefreshToken
    {
        return oxNew(RefreshToken::class);
    }
}
