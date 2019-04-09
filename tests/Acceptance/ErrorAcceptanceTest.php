<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use OxidEsales\GraphQl\DataObject\Token;

class ErrorAcceptanceTest extends BaseGraphQlAcceptanceTestCase
{

    public function testSyntaxError()
    {
        $query = "das ist keine query";
        $this->executeQuery($query);
        $this->assertEquals(400, $this->httpStatus);
        $this->assertErrorMessage('Syntax Error: Unexpected Name "das"');
    }

    public function testExpiredToken()
    {
        $token = $this->createToken('anonymous');

        // Setting issue date and expiry 5 resp. 4 day into the past
        $jwtObjectProperty = new \ReflectionProperty(Token::class, 'jwtObject');
        $jwtObjectProperty->setAccessible(true);
        $jwtObject = $jwtObjectProperty->getValue($token);
        $jwtObject->iss = $jwtObject->iss - 5 * 24 * 60 * 60;
        $jwtObject->exp = $jwtObject->iss + 1 * 24 * 60 * 60;
        $jwtObjectProperty->setValue($token, $jwtObject);

        $this->executeQueryWithToken('query LoginTest {setlanguage (lang: "fr") {token} }', $token);
        $this->assertEquals(401, $this->httpStatus);
        $this->assertErrorMessage('Expired token');
    }

    public function testMissingParameter()
    {
        $this->executeQuery('query LoginTest {setlanguage {token} }');
        $this->assertEquals(400, $this->httpStatus);
        $this->assertErrorMessage('Field "setlanguage" argument "lang" of type "String!" is required but not provided.');
    }

    public function testIllegalReturnFieldRequest()
    {
        $this->executeQuery('query LoginTest {setlanguage (lang: "fr") {unknown} }');
        $this->assertEquals(400, $this->httpStatus);
        $this->assertErrorMessage('Cannot query field "unknown" on type "Login".');
    }
}
