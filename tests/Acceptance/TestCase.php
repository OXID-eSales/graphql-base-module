<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Acceptance;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use OxidEsales\GraphQL\Framework\ResponseWriterInterface;
use OxidEsales\GraphQL\Framework\ResponseWriter;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Framework\GraphQLQueryHandlerInterface;

abstract class TestCase extends PHPUnitTestCase
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

    protected function setUp(): void
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
        
        unset($_REQUEST['query']);
    }

    protected function execQuery(string $query)
    {
        $_REQUEST['query'] = $query;
        $this->container->get(GraphQLQueryHandlerInterface::class)
                        ->executeGraphQLQuery();
        unset($_REQUEST['query']);
    }

    public static function tearDownAfterClass(): void
    {
        unset($_REQUEST['query']);
    }
}
