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
    protected static $queryResult = null;
    protected static $logResult = null;
    protected static $container = null;

    public static function responseCallback($body, $status)
    {
        self::$queryResult = [
            'status' => $status,
            'body' => $body
        ];
    }

    public static function loggerCallback(string $message)
    {
        self::$logResult .= $message;
    }

    /**
     * this empty methods prevents phpunit from resetting
     * invocation mocker and therefore we can use the same
     * mocks for all tests and do not need to reinitialize
     * the container for every test in this file which
     * makes the whole thing pretty fast :-)
     */
    protected function verifyMockObjects()
    {
    }

    protected function tearDown(): void
    {
        static::$queryResult = null;
        static::$logResult = null;
    }

    protected function setUp(): void
    {
        if (self::$container !== null) {
            return;
        }
        $containerFactory = new TestContainerFactory();
        self::$container = $containerFactory->create();

        $responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $responseWriter->method('renderJsonResponse')
                       ->willReturnCallback([
                           TestCase::class,
                           'responseCallback'
                       ]);
        self::$container->set(
            ResponseWriterInterface::class,
            $responseWriter
        );
        self::$container->autowire(
            ResponseWriterInterface::class,
            ResponseWriter::class
        );

        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')
                    ->willReturn(base64_encode(random_bytes(64)));
        self::$container->set(
            KeyRegistryInterface::class,
            $keyRegistry
        );
        self::$container->autowire(
            KeyRegistryInterface::class,
            KeyRegistry::class
        );

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->method('error')
               ->willReturnCallback([
                   TestCase::class,
                   'loggerCallback'
               ]);
        self::$container->set(
            LoggerInterface::class,
            $logger
        );
        self::$container->autowire(
            LoggerInterface::class,
            get_class($logger)
        );

        self::$container->compile();
        
        unset($_REQUEST['query']);
    }

    protected function execQuery(string $query)
    {
        $_REQUEST['query'] = $query;
        self::$container->get(GraphQLQueryHandlerInterface::class)
                        ->executeGraphQLQuery();
        unset($_REQUEST['query']);
    }

    public static function tearDownAfterClass(): void
    {
        unset($_REQUEST['query']);
    }
}
