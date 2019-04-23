<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\DataObject;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\InsufficientData;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    const TEST_KEY = '1234567890123456';

    public function testJWTGenerationMissingData()
    {
        $this->setExpectedException(InsufficientData::class);

        $token = new Token();
        $token->getJwt($this::TEST_KEY);
    }

    public function testJWTEncodingDecoding()
    {
        $token = new Token();
        $token->setSubject('someuser');
        $token->setLang('de');
        $token->setShopid(1);
        $token->setShopUrl("https://localhost");
        $token->setUserGroup('customers');
        $jwt = $token->getJwt($this::TEST_KEY);

        $newToken = new Token();
        $newToken->setJwt($jwt, $this::TEST_KEY);

        $this->assertEquals($token->getSubject(), $newToken->getSubject());
        $this->assertEquals($token->getUserGroup(), $newToken->getUserGroup());
        $this->assertEquals($token->getLang(), $newToken->getLang());
        $this->assertEquals($token->getShopid(), $newToken->getShopid());
        $this->assertEquals($token->getIssuer(), $newToken->getIssuer());
        $this->assertEquals($token->getAudience(), $newToken->getAudience());
        $this->assertEquals($token->getIssueDate(), $newToken->getIssueDate());
        $this->assertEquals($token->getTokenId(), $newToken->getTokenId());
        $this->assertEquals($token->getExpiryDate(), $newToken->getExpiryDate());
        $this->assertEquals($newToken->getExpiryDate() - (31 * 24 * 60 * 60), $newToken->getIssueDate());
    }
}
