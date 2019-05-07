<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use GraphQL\Executor\ExecutionResult;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandlerInterface;
use OxidEsales\GraphQl\Framework\RequestReader;
use OxidEsales\GraphQl\Framework\RequestReaderInterface;
use OxidEsales\GraphQl\Framework\ResponseWriter;
use OxidEsales\GraphQl\Framework\ResponseWriterInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;

class BaseGraphQlAcceptanceTestCase extends UnitTestCase
{

    /** @var  RequestReaderInterface|MockObject */
    protected $requestReader;

    /** @var  ResponseWriterInterface|MockObject */
    private $responseWriter;

    /** @var  EnvironmentServiceInterface */
    private $environmentService;

    /** @var  ExecutionResult */
    protected $queryResult;

    /** @var int */
    protected $httpStatus;

    /** @var  string */
    protected $logResult = "";

    /** @var Container */
    protected $container;

    /** @var  string */
    protected $lang;

    /** @var  int */
    protected $shopId;

    /** @var  string */
    protected $signatureKey;

    public function responseCallback($result, $httpStatus)
    {
        $this->queryResult = $result;
        $this->httpStatus = $httpStatus;
    }

    public function loggerCallback(string $logmessage)
    {
        $this->logResult .= $logmessage;
    }

    public function setUp()
    {
        $this->queryResult = null;
        $this->httpStatus = null;
        $this->logResult = "";

        $containerFactory = new TestContainerFactory();
        $this->container = $containerFactory->create();
        $this->requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $this->responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $this->responseWriter->method('renderJsonResponse')->willReturnCallback([$this, 'responseCallback']);
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->method('error')->willReturnCallback([$this, 'loggerCallback']);
        $this->container->set(RequestReaderInterface::class, $this->requestReader);
        $this->container->autowire(RequestReaderInterface::class, RequestReader::class);
        $this->container->set(ResponseWriterInterface::class, $this->responseWriter);
        $this->container->autowire(ResponseWriterInterface::class, ResponseWriter::class);
        $this->container->set(LoggerInterface::class, $logger);
        $this->container->autowire(LoggerInterface::class, get_class($logger));
        $this->beforeContainerCompile();
        $this->container->compile();
        $this->environmentService = $this->container->get(EnvironmentServiceInterface::class);
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->container->get(KeyRegistryInterface::class);
        $this->signatureKey = $keyRegistry->getSignatureKey();
    }

    protected function beforeContainerCompile()
    {
        // Overwrite this in Testsetup if you need to change more in the container
    }

    protected function executeQueryWithToken($query, Token $token)
    {
        $this->requestReader->method('getAuthorizationHeader')
            ->willReturn('Bearer ' . $token->getJwt($this->signatureKey));
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);

        $queryHandler = $this->container->get(GraphQlQueryHandlerInterface::class);
        $queryHandler->executeGraphQlQuery();

    }

    public function executeQuery($query)
    {
        $token = $this->createToken('anonymous');
        $this->executeQueryWithToken($query, $token);

    }

    private function createAuthHeader($userGroup, $lang, $shopId)
    {
        return 'Bearer ' . $this->createToken($userGroup, $lang, $shopId)->getJwt($this->signatureKey);

    }

    protected function createToken($userGroup) {

        $token = new Token();

        $token->setUserGroup($userGroup);
        $token->setKey('somekey');
        $token->setSubject($this->environmentService->getShopUrl());
        $token->setShopUrl($this->environmentService->getShopUrl());
        $token->setLang('de');
        $token->setShopid(1);

        return $token;
    }
    
    public function getShopId() {
        return $this->shopId;
    }

    public function getLang() {
        return $this->lang;
    }

    public function assertErrorMessage(string $message)
    {
        $this->assertEquals($message, $this->queryResult['errors'][0]['message']);
    }

    public function assertErrorMessageContains(string $messageFragment)
    {
        $this->assertContains($messageFragment, $this->queryResult['errors'][0]['message']);
    }

    public function assertLogMessageContains(string $messageFragment)
    {
        $this->assertContains($messageFragment, $this->logResult);
    }

    public function assertHttpStatus(int $status)
    {
        $this->assertEquals($status, $this->httpStatus);
    }

    public function assertHttpStatusOK()
    {
        $this->assertHttpStatus(200);
    }
}
