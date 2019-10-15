<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\DataObject;

/**
 * Wraps the JWT Token class to provide a single
 * and typed interface to the claims that we use
 */
class Token extends \Lcobucci\JWT\Token
{
    const CLAIM_SHOPID   = 'shopid';
    const CLAIM_LANGID   = 'shopid';
    const CLAIM_USERNAME = 'username';
    const CLAIM_GROUP    = 'group';

    public function getShopId(): int
    {
        return $this->getClaim(self::CLAIM_SHOPID);
    }

    public function getLangId(): int
    {
        return $this->getClaim(self::CLAIM_LANGID);
    }

    public function getUsername(): string
    {
        return $this->getClaim(self::CLAIM_USERNAME);
    }

    public function getGroup(): string
    {
        return $this->getClaim(self::CLAIM_GROUP);
    }
}
