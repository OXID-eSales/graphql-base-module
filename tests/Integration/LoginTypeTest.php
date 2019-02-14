<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidProfessionalServices\GraphQl\Tests\Integration;

use GraphQL\GraphQL;
use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Framework\TypeFactory;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Type\LoginType;
use OxidEsales\GraphQl\Type\Mutation;
use OxidEsales\GraphQl\Type\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class LoginTypeTest extends TestCase
{
    public function testLoginToken()
    {
        $queryFactory = new TypeFactory(Query::class);
        $mutationFactory = new TypeFactory(Mutation::class);
        /** @var MockObject|AuthenticationServiceInterface $authService */
        $authService = $this->getMockBuilder(AuthenticationServiceInterface::class)->getMock();
        $authService->method('getToken')->willReturn('This is a dummy token');
        $loginType = new LoginType($authService);
        $queryFactory->addSubType($loginType);

        $schemaFactory = new SchemaFactory($queryFactory, $mutationFactory);

        $schema = $schemaFactory->getSchema();
        $query = <<< EOQ
query TestLogin {
    login (username: "someuser", password: "password", lang: "en", shopid: 25) {
        token       
    }
}
EOQ;
        $graphQl = new GraphQL();
        $result = $graphQl->executeQuery(
            $schema,
            $query
        );

        $this->assertEquals('This is a dummy token', $result->data['login']['token']);

    }

}
