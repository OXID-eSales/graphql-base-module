<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\Service\Token as TokenService;

final class Token
{
    /** @var TokenService */
    private $tokenService;

    public function __construct(
        TokenService $tokenService
    ) {
        $this->tokenService = $tokenService;
    }
}
