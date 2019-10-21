<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration\Framework;

use OxidEsales\GraphQL\Tests\Integration\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GraphQLQueryHandlerTest extends TestCase
{

    protected static function beforeContainerCompile()
    {
        $loader = new YamlFileLoader(static::$container, new FileLocator());
        $serviceFile = __DIR__ . DIRECTORY_SEPARATOR . 'services.yml';
        $loader->load($serviceFile);
    }

    public function testExceptionInRoute()
    {
        static::$container = null;
        $this->setUp();
        static::$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';
        $this->execQuery('query { testQuery(foo: "bar") }');
        $this->assertEquals(
            403,
            static::$queryResult['status']
        );
        static::$container = null;
    }

    public function testLoginWithValidCredentialsWithOperationName()
    {
        $this->execQuery(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'fooBar'
        );

        $this->assertEquals(
            200,
            static::$queryResult['status']
        );
    }

    public function testLoginWithValidCredentialsWithWrongOperationName()
    {
        $this->execQuery(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'noOp'
        );

        $this->assertEquals(
            400,
            static::$queryResult['status']
        );
    }

    public function testNonExistantQuery()
    {
        $this->execQuery('query { nonExistant }');
        $this->assertEquals(
            400,
            static::$queryResult['status']
        );
    }
}
