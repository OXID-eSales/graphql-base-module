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

    /*
    public function testExceptionInRoute()
    {
        self::$container = null;
        $this->setUp();
        self::$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';
        $this->execQuery('query { token }');
        $this->assertEquals(
            403,
            self::$queryResult['status']
        );
        self::$container = null;
    }
     */

    public function testLoginWithValidCredentialsWithOperationName()
    {
        $this->execQuery(
            'query loginOperation { token (username: "admin", password: "admin") }',
            null,
            'loginOperation'
        );

        $this->assertEquals(
            200,
            self::$queryResult['status']
        );
    }

    public function testLoginWithValidCredentialsWithWrongOperationName()
    {
        $this->execQuery(
            'query loginOperation { token (username: "admin", password: "admin") }',
            null,
            'noOp'
        );

        $this->assertEquals(
            400,
            self::$queryResult['status']
        );
    }
}
