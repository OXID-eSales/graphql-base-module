<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GraphQLQueryHandlerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        static::$container = null;
    }

    protected static function beforeContainerCompile()
    {
        $loader = new YamlFileLoader(static::$container, new FileLocator());
        $serviceFile = __DIR__ . DIRECTORY_SEPARATOR . 'services.yml';
        $loader->load($serviceFile);
    }

    public function testClientAwareExceptionInRoute()
    {
        $this->execQuery('query { clientAwareExceptionQuery(foo: "bar") }');
        $this->assertEquals(
            403,
            static::$queryResult['status']
        );
        $this->assertEquals(
            'invalid token message',
            static::$queryResult['body']['errors'][0]['message']
        );
    }

    public function testExceptionInRoute()
    {
        $this->execQuery('query { exceptionQuery(foo: "bar") }');
        $this->assertEquals(
            400,
            static::$queryResult['status']
        );
        $this->assertEquals(
            'Internal server error',
            static::$queryResult['body']['errors'][0]['message']
        );
    }

    public function testQueryWithOperationName()
    {
        $this->execQuery(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'fooBar'
        );

        $this->assertEquals(
            [
                'status' => 200,
                'body'   => [
                    'data' => [
                        'testQuery' => 'bar'
                    ]
                ]
            ],
            static::$queryResult
        );
    }

    public function testQueryWithWrongOperationName()
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

    public function testInvalidQuery()
    {
        $this->execQuery('FOOBAR');
        $this->assertEquals(
            400,
            static::$queryResult['status']
        );
    }

    public function testLoggedQuery()
    {
        $this->execQuery('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken(static::$queryResult['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $this->execQuery('query { testLoggedQuery(foo: "bar") }');
        $this->assertEquals(
            [
                'status' => 200,
                'body'   => [
                    'data' => [
                        'testLoggedQuery' => 'bar'
                    ]
                ]
            ],
            static::$queryResult
        );
        static::$container = null;
    }

}
