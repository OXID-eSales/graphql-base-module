<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration\Framework;

use OxidEsales\GraphQL\Tests\Integration\TestCase;

class GraphQLQueryHandlerTest extends TestCase
{
    public function testVariablesWithQuery()
    {
        $this->execQuery('query { token (username: "admin", password: "admin") }');

        $this->assertEquals(
            200,
            self::$queryResult['status']
        );

    }

    public function testExceptionInRoute()
    {
        self::$container = null;
        $this->setUp();
        self::$token = 'invalid';
        $this->execQuery('query { token }');
        $this->assertEquals(
            403,
            self::$queryResult['status']
        );
        self::$container = null;
    }
}
