<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use DateTimeInterface;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

class NullToken extends Token
{
    public function __construct()
    {
        // ignore all parameters to parent
        parent::__construct();
    }

    public function verify(Signer $signer, $key)
    {
        return true;
    }

    public function validate(ValidationData $data)
    {
        return true;
    }

    public function isExpired(?DateTimeInterface $now = null)
    {
        return false;
    }
}
