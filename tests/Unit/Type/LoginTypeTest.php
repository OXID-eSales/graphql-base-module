<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidProfessionalServices\GraphQl\Tests\Unit\Type;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Framework\TypeFactory;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Type\LoginType;
use OxidEsales\GraphQl\Type\Mutation;
use OxidEsales\GraphQl\Type\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginTypeTest extends TestCase
{

    /** @var  Schema $schema */
    private $schema;

    public function setUp()
    {
        $queryFactory = new TypeFactory(Query::class);
        $mutationFactory = new TypeFactory(Mutation::class);
        $token = new Token();
        $token->setSubject('somesubject');
        $token->setUserGroup('somegroup');
        $token->setLang('de');
        $token->setShopid(1);
        $token->setShopUrl('http://somethingorother.com');
        /** @var MockObject|AuthenticationServiceInterface $authService */
        $authService = $this->getMockBuilder(AuthenticationServiceInterface::class)->getMock();
        $authService->method('getToken')->willReturn($token);
        /** @var MockObject|KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')->willReturn('1234567890123456');
        $loginType = new LoginType($authService, $keyRegistry);
        $queryFactory->addSubType($loginType);

        $schemaFactory = new SchemaFactory($queryFactory, $mutationFactory);

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
        $token->setJwt($jwt, '1234567890123456');
        $this->assertEquals('http://somethingorother.com', $token->getIssuer());

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
        $token->setJwt($jwt, '1234567890123456');
        $this->assertEquals('http://somethingorother.com', $token->getIssuer());

    }

    private function executeQuery($query)
    {
        $graphQl = new GraphQL();
        $context = new AppContext();
        $context->setDefaultShopLanguage('de');
        $context->setDefaultShopId(1);
        return $graphQl->executeQuery(
            $this->schema,
            $query,
            null,
            $context
        );

    }
}
