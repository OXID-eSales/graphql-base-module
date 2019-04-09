<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandlerInterface;

/**
 * Class LoginAcceptanceTest
 *
 * @package OxidEsales\GraphQl\Tests\Acceptance
 */
class LoginAcceptanceTest extends BaseGraphQlAcceptanceTestCase
{

    /**
     * Testing login without username and password
     */
    public function testAnonymousLogin()
    {
        $query = "query LoginTest {login {token} }";

        $this->executeQuery($query);

        $this->assertEquals(200, $this->httpStatus);
        $token = new Token();
        $token->setJwt($this->queryResult['data']['login']['token'], $this->signatureKey);
        $this->assertEquals('anonymous', $token->getUserGroup());
        $this->assertTrue(0 == strlen($this->logResult));
    }

    /**
     * Testing with existing username and correct password
     */
    public function testUserLogin()
    {
        $query = 'query LoginTest {login (username: "admin", password: "admin") {token} }';

        $this->executeQueryWithoutAuthHeader($query);

        $this->assertEquals(200, $this->httpStatus);
        $token = new Token();
        $token->setJwt($this->queryResult['data']['login']['token'], $this->signatureKey);
        $this->assertEquals('admin', $token->getUserGroup());
        $this->assertTrue(0 == strlen($this->logResult));
    }

    /**
     * Testing with existing username and wrong password
     */
    public function testWrongPassword()
    {
        $query = 'query LoginTest {login (username: "admin", password: "wrong") {token} }';

        $this->executeQueryWithoutAuthHeader($query);

        $this->assertEquals(401, $this->httpStatus);
        $this->assertErrorMessage('User/password combination is not valid.');
        $this->assertTrue(0 === strpos($this->logResult, 'User/password combination is not valid.'));
    }

    /** Change the language in the token */
    public function testChangeLanguage()
    {
        $query = 'query LoginTest {setlanguage (lang: "fr") {token} }';

        $token = $this->createToken('anonymous');
        $token->setLang('de');

        $this->executeQueryWithToken($query, $token);

        $this->assertEquals(200, $this->httpStatus);
        $newToken = new Token();
        $newToken->setJwt($this->queryResult['data']['setlanguage']['token'], $this->signatureKey);
        $this->assertEquals('fr', $newToken->getLang());
    }

    /**
     * @param string $query
     */
    public function executeQueryWithoutAuthHeader(string $query)
    {
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);
        $this->requestReader->method('getAuthorizationHeader')
            ->willThrowException(new NoAuthHeaderException());

        $queryHandler = $this->container->get(GraphQlQueryHandlerInterface::class);
        $queryHandler->executeGraphQlQuery();
    }
}
