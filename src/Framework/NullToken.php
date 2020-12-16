<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use DateTimeInterface;
use Lcobucci\JWT\Token;

class NullToken extends Token
{
    public function __construct()
    {
        // ignore all parameters to parent
        parent::__construct();
    }

    public function isExpired(?DateTimeInterface $now = null)
    {
        return false;
    }
}
