<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

class LoginAcceptanceTest extends BaseGraphQlAcceptanceTestCase
{
    private $signingKey;

    public function setUp()
    {
        parent::setUp();
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->container->get(KeyRegistryInterface::class);
        $this->signingKey = $keyRegistry->getSignatureKey();
    }

    public function testAnonymousLogin()
    {
        $query = "query LoginTest {login {token} }";

        $this->executeQuery($query, null);

        $this->assertEquals(200, $this->httpStatus);
        $token = new Token();
        $token->setJwt($this->queryResult['data']['login']['token'], $this->signingKey);
        $this->assertEquals('anonymous', $token->getUserGroup());
        $this->assertTrue(0 == strlen($this->logResult));
    }

    public function testUserLogin()
    {
        $query = 'query LoginTest {login (username: "admin", password: "admin") {token} }';

        $this->executeQuery($query, null);

        $this->assertEquals(200, $this->httpStatus);
        $token = new Token();
        $token->setJwt($this->queryResult['data']['login']['token'], $this->signingKey);
        $this->assertEquals('admin', $token->getUserGroup());
        $this->assertTrue(0 == strlen($this->logResult));
    }

    public function testWrongPassword()
    {
        $query = 'query LoginTest {login (username: "admin", password: "wrong") {token} }';

        $this->executeQuery($query, null);

        $this->assertEquals(401, $this->httpStatus);
        $this->assertEquals('User/password combination is not valid.', $this->queryResult['errors'][0]['message']);
        $this->assertTrue(0 === strpos($this->logResult, 'User/password combination is not valid.'));

    }

}
