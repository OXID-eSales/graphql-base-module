<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Executor\ExecutionResult;
use OxidEsales\GraphQl\Exception\HttpErrorInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use Psr\Log\LoggerInterface;

class GraphQlQueryHandler implements GraphQlQueryHandlerInterface
{

    /** @var LoggerInterface  */
    private $logger;
    /** @var EnvironmentServiceInterface  */
    private $environmentService;
    /** @var SchemaFactoryInterface  */
    private $schemaFactory;
    /** @var KeyRegistryInterface  */
    private $keyRegistry;
    /** @var ErrorCodeProviderInterface  */
    private $errorCodeProvider;
    /** @var  RequestReaderInterface */
    private $requestReader;
    /** @var  ResponseWriterInterface */
    private $responseWriter;

    private $loggingErrorFormatter;

    public function __construct(
        LoggerInterface $logger,
        EnvironmentServiceInterface $environmentService,
        SchemaFactoryInterface $schemaFactory,
        KeyRegistryInterface $keyRegistry,
        ErrorCodeProviderInterface $errorCodeProvider,
        RequestReaderInterface $requestReader,
        ResponseWriterInterface $responseWriter
    ) {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
        $this->schemaFactory = $schemaFactory;
        $this->errorCodeProvider = $errorCodeProvider;
        $this->keyRegistry = $keyRegistry;
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;

        $this->loggingErrorFormatter = function (Error $error) {
            $this->logger->error($error);
            return FormattedError::createFromException($error);
        };
    }

    public function executeGraphQlQuery()
    {
        $httpStatus = null;

        try {
            $queryData = $this->requestReader->getGraphQLRequestData();
            $result = $this->executeQuery($queryData);
        } catch (\Exception $e) {
            $reflectionClass = new \ReflectionClass($e);
            if ($e instanceof HttpErrorInterface) {
                // Thank god. Our own exceptions provide a http status.
                /** @var HttpErrorInterface $e */
                $httpStatus = $e->getHttpStatus();
            }
            $result = $this->createErrorResult($e);
        }
        if (is_null($httpStatus)) {
            $httpStatus = $this->errorCodeProvider->getHttpReturnCode($result);
        }
        $result->setErrorFormatter($this->loggingErrorFormatter);
        $this->responseWriter->renderJsonResponse($result->toArray(), $httpStatus);
    }

    private function createErrorResult(\Exception $e): ExecutionResult
    {
        $msg = $e->getMessage();
        if (! $msg) {
            $msg = 'Unknown error: ' . $e->getTraceAsString();
        }
        $error = new Error($msg);
        $result = new ExecutionResult(null, [$error]);
        return $result;
    }

    /**
     * Execute the GraphQL query
     *
     * @throws \Throwable
     */
    private function executeQuery($queryData)
    {
        $graphQL = new \GraphQL\GraphQL();
        $variables = null;
        if (isset($queryData['variables'])) {
            $variables = (array) $queryData['variables'];
        }
        $operationName = null;
        if (isset($queryData['operationName'])) {
            $operationName = $queryData['operationName'];
        }
        $result = $graphQL->executeQuery(
            $this->schemaFactory->getSchema(),
            $queryData['query'],
            null,
            null,
            $variables,
            $operationName
        );
        return $result;
    }
}
