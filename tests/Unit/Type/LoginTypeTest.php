<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Type;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Type\ObjectType\LoginType;
use OxidEsales\GraphQl\Type\Provider\LoginQueryProvider;
use PHPUnit\Framework\MockObject\MockObject;

class LoginTypeTest extends GraphQlTypeTestCase
{
    const SIGNATURE_KEY = '1234567890123456';

    public function setUp()
    {
        parent::setUp();

        /** @var \PHPUnit_Framework_MockObject_MockObject|AuthenticationServiceInterface $authService */
        $authService = $this->getMockBuilder(AuthenticationServiceInterface::class)->getMock();
        $authService->method('getToken')->willReturnCallback(function () {return $this->token;});
        /** @var \PHPUnit_Framework_MockObject_MockObject|KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')->willReturn($this::SIGNATURE_KEY);
        $loginQueryProvider = new LoginQueryProvider(
            $authService,
            $keyRegistry,
            $this->permissionsService,
            new LoginType());

        $this->addPermission('somegroup', 'mayreaddata');

        $schemaFactory = new SchemaFactory();
        $schemaFactory->addQueryProvider($loginQueryProvider);

        $this->schema = $schemaFactory->getSchema();

    }

    public function testUserLoginToken()
    {
        $query = <<< EOQ
query TestLogin {
    login (username: "someuser", password: "password", lang: "en", shopid: 25) {
        token       
    }
}
EOQ;

        $result = $this->executeQuery($query);
        $jwt = $result->data['login']['token'];
        $token = new Token();
        $token->setJwt($jwt, $this::SIGNATURE_KEY);
        $this->assertEquals('http://somethingorother.com', $token->getIssuer());
        $this->assertEquals('initialtokenkey', $token->getKey());

    }

    public function testChangeLanguage()
    {
        $query = <<< EOQ
query TestLogin {
    setlanguage (lang: "en") {
        token       
    }
}
EOQ;
        $token = $this->createDefaultToken();

        $context = $this->createDefaultContext();
        $context->setAuthToken($token);
        $result = $this->executeQuery($query, $context);
        $jwt = $result->data['setlanguage']['token'];

        $newtoken = new Token();
        $newtoken->setJwt($jwt, $this::SIGNATURE_KEY);
        $this->assertEquals('en', $newtoken->getLang());
    }

    public function testChangeLanguageWithoutParameter()
    {
        $query = <<< EOQ
query TestLogin {
    setlanguage {
        token       
    }
}
EOQ;
        $token = $this->createDefaultToken();

        $context = $this->createDefaultContext();
        $context->setAuthToken($token);
        $result = $this->executeQuery($query, $context);
        $this->assertEquals(1, count($result->errors));
        $this->assertEquals(
            'Field "setlanguage" argument "lang" of type "String!" is required but not provided.',
            $result->errors[0]->message);
        $this->assertNull($result->data);

    }

    public function testChangeLanguageWithoutToken()
    {
        $query = <<< EOQ
query TestLogin {
    setlanguage (lang: "en") {
        token       
    }
}
EOQ;
        $result = $this->executeQuery($query, new AppContext());
        $this->assertEquals(1, count($result->errors));
        $this->assertEquals(
            'Missing Permission: User is not autheticated. Did you send an Authorization header with ' .
            'content "Bearer <token>" where token is a token received by a login query?',
            $result->errors[0]->message);
        $this->assertNull($result->data['setlanguage']);

    }

    public function testAnonymousLoginToken()
    {
        $query = <<< EOQ
query TestLogin {
    login {
        token       
    }
}
EOQ;
        $result = $this->executeQuery($query);
        $jwt = $result->data['login']['token'];
        $token = new Token();
        $token->setJwt($jwt, $this::SIGNATURE_KEY);
        $this->assertEquals('http://somethingorother.com', $token->getIssuer());

    }

}
