<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use OxidEsales\GraphQL\Framework\ResponseWriterInterface;
use OxidEsales\GraphQL\Framework\ResponseWriter;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use OxidEsales\GraphQL\Service\KeyRegistry;
use Psr\Log\LoggerInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Framework\GraphQLQueryHandlerInterface;

abstract class TestCase extends PHPUnitTestCase
{
    protected $queryResult = null;
    protected $logResult = null;
    protected $container = null;

    public function responseCallback($body, $status)
    {
        $this->queryResult = [
            'status' => $status,
            'body' => $result
        ];
    }

    public function loggerCallback(string $message)
    {
        $this->logResult .= $message;
    }

    protected function setUp(): void
    {
        $containerFactory = new TestContainerFactory();
        $this->container = $containerFactory->create();

        $responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $responseWriter->method('renderJsonResponse')
                       ->willReturnCallback([
                           $this,
                           'responseCallback'
                       ]);
        $this->container->set(
            ResponseWriterInterface::class,
            $responseWriter
        );
        $this->container->autowire(
            ResponseWriterInterface::class,
            ResponseWriter::class
        );

        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')
                    ->willReturn('as8dhfasuhef.a8hefa8hefiauhefaishefo9sh4of89h4o8fhso84hf.oas8h4fo8wh4foawfasdfaskdhf.asdfa');
        $this->container->set(
            KeyRegistryInterface::class,
            $keyRegistry
        );
        $this->container->autowire(
            KeyRegistryInterface::class,
            KeyRegistry::class
        );

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->method('error')
               ->willReturnCallback([
                   $this,
                   'loggerCallback'
               ]);
        $this->container->set(
            LoggerInterface::class,
            $logger
        );
        $this->container->autowire(
            LoggerInterface::class,
            get_class($logger)
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
