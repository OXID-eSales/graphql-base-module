<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\InsufficientTokenData;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testJWTGenerationMissingData()
    {
        $this->expectException(InsufficientTokenData::class);

        $token = new Token("1234567890123456");
        $token->getJwt();
    }

    public function testJWTEncodingDecoding()
    {
        $token = new Token('1234567890123456');
        $token->setSubject('someuser');
        $token->setLang('de');
        $token->setShopid(1);
        $token->setShopUrl("https://localhost");
        $token->setUserGroup('customers');
        $jwt = $token->getJwt();

        $newToken = new Token('1234567890123456');
        $newToken->setJwt($jwt);

        $this->assertEquals($token->getExpiryDate(), $newToken->getExpiryDate());
        $this->assertEquals($newToken->getExpiryDate() - (365 * 24 * 60 * 60), $newToken->getIssueDate());
    }
}
