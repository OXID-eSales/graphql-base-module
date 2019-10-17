<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration\Controller;

use OxidEsales\GraphQL\Tests\Integration\TestCase;

class LoginTest extends TestCase
{
    public function testLoginWithMissingCredentials()
    {
        $this->execQuery('query { token }');

        $this->assertEquals(
            400,
            $this->queryResult['status']
        );

        unset($_REQUEST['query']);
    }


    public function testLoginWithWrongCredentials()
    {
        $this->execQuery('query { token (username: "foo", password: "bar") }');

        $this->assertEquals(
            401,
            $this->queryResult['status']
        );

        unset($_REQUEST['query']);
    }

    public function testLoginWithValidCredentials()
    {
        $this->execQuery('query { token (username: "admin", password: "admin") }');

        $this->assertEquals(
            200,
            $this->queryResult['status']
        );
        
        unset($_REQUEST['query']);
    }

}
