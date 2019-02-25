<?php declare(strict_types=1);

namespace OxidEsales\GraphQl\Tests\Unit\Type;

use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Framework\TypeFactory;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Type\LoginType;
use OxidEsales\GraphQl\Type\Mutation;
use OxidEsales\GraphQl\Type\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

class SchemaTest extends TestCase
{

    /**
     * Test
     */
    public function testSchemaFactory()
    {
        $queryFactory = new TypeFactory(Query::class);
        $mutationFactory = new TypeFactory(Mutation::class);
        /** @var MockObject|AuthenticationServiceInterface $authService */
        $authService = $this->getMockBuilder(AuthenticationServiceInterface::class)->getMock();
        /** @var MockObject|KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $loginType = new LoginType($authService, $keyRegistry);
        $queryFactory->addSubType($loginType);

        $schemaFactory = new SchemaFactory($queryFactory, $mutationFactory);

        $schema = $schemaFactory->getSchema();

        $this->assertNotNull($schema);
    }
}
