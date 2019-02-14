<?php declare(strict_types=1);

namespace OxidEsales\GraphQl\Tests\Integration;

use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Framework\TypeFactory;
use OxidEsales\GraphQl\Service\AuthenticationService;
use OxidEsales\GraphQl\Type\LoginType;
use OxidEsales\GraphQl\Type\Mutation;
use OxidEsales\GraphQl\Type\Query;
use PHPUnit\Framework\TestCase;

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

class SchemaTest extends TestCase
{

    public function testSchemaFactory()
    {
        $queryFactory = new TypeFactory(Query::class);
        $mutationFactory = new TypeFactory(Mutation::class);
        $authService = new AuthenticationService();
        $loginType = new LoginType($authService);
        $queryFactory->addSubType($loginType);

        $schemaFactory = new SchemaFactory($queryFactory, $mutationFactory);

        $schema = $schemaFactory->getSchema();

        $this->assertNotNull($schema);

    }

}
