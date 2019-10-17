<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Acceptance\Controller;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Framework\ResponseWriterInterface;
use OxidEsales\GraphQL\Framework\ResponseWriter;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Framework\GraphQLQueryHandlerInterface;

class LoginTest extends TestCase
{

    protected $queryResult = null;
    protected $container = null;

    public function responseCallback($body, $status)
    {
        $this->queryResult = [
            'status' => $status,
            'body' => $result
        ];
    }

    public function setUp()
    {
        $containerFactory = new TestContainerFactory();
        $this->container = $containerFactory->create();

        $responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $responseWriter->method('renderJsonResponse')
                       ->willReturnCallback([$this, 'responseCallback']);
        $this->container->set(
            ResponseWriterInterface::class,
            $responseWriter
        );

        $this->container->autowire(
            ResponseWriterInterface::class,
            ResponseWriter::class
        );

        $this->container->compile();

    }

    public function testLoginWithMissingCredentials()
    {
        $_REQUEST['query'] = "query { token }";

        $this->container->get(GraphQLQueryHandlerInterface::class)
                        ->executeGraphQLQuery();

        $this->assertEquals(
            400,
            $this->queryResult['status']
        );

        unset($_REQUEST['query']);
    }


    public function testLoginWithWrongCredentials()
    {
        $_REQUEST['query'] = "query { token (username: \"foo\", password: \"bar\") }";

        $this->container->get(GraphQLQueryHandlerInterface::class)
                        ->executeGraphQLQuery();

        $this->assertEquals(
            401,
            $this->queryResult['status']
        );

        unset($_REQUEST['query']);
    }

    public function testLoginWithValidCredentials()
    {
        $_REQUEST['query'] = "query { token (username: \"test\", password: \"test\") }";

        $this->container->get(GraphQLQueryHandlerInterface::class)
                        ->executeGraphQLQuery();

        $this->assertEquals(
            200,
            $this->queryResult['status']
        );
        
        unset($_REQUEST['query']);
    }

}
