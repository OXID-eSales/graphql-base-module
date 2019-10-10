<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Executor\ExecutionResult;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\HttpErrorInterface;
use OxidEsales\GraphQl\Exception\InvalidTokenException;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Service\TokenServiceInterface;
use OxidEsales\GraphQl\Utility\LegacyWrapperInterface;
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
    /** @var TokenServiceInterface  */
    private $tokenService;
    /** @var  LegacyWrapperInterface $legacyWrapper */
    private $legacyWrapper;

    private $loggingErrorFormatter;

    public function __construct(
        LoggerInterface $logger,
        EnvironmentServiceInterface $environmentService,
        SchemaFactoryInterface $schemaFactory,
        KeyRegistryInterface $keyRegistry,
        ErrorCodeProviderInterface $errorCodeProvider,
        RequestReaderInterface $requestReader,
        ResponseWriterInterface $responseWriter,
        TokenServiceInterface $tokenService,
        LegacyWrapperInterface $legacyWrapper
    ) {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
        $this->schemaFactory = $schemaFactory;
        $this->errorCodeProvider = $errorCodeProvider;
        $this->keyRegistry = $keyRegistry;
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;
        $this->tokenService = $tokenService;
        $this->legacyWrapper = $legacyWrapper;

        $this->loggingErrorFormatter = function (Error $error) {
            $this->logger->error($error);
            return FormattedError::createFromException($error);
        };
    }

    public function executeGraphQlQuery()
    {
        $httpStatus = null;

        try {
            $context = $this->initializeAppContext();
            $queryData = $this->requestReader->getGraphQLRequestData();
            $result = $this->executeQuery($context, $queryData);
        } catch (\Exception $e) {
            $reflectionClass = new \ReflectionClass($e);
            if ($e instanceof HttpErrorInterface) {
                // Thank god. Our own exceptions provide a http status.
                /** @var HttpErrorInterface $e */
                $httpStatus = $e->getHttpStatus();
            } elseif ($reflectionClass->getNamespaceName() == 'Firebase\JWT') {
                // Authentication failed. Something with the token went wrong.
                $httpStatus = 401;
            }
            $result = $this->createErrorResult($e);
        }
        if (is_null($httpStatus)) {
            $httpStatus = $this->errorCodeProvider->getHttpReturnCode($result);
        }
        $result->setErrorFormatter($this->loggingErrorFormatter);
        $this->responseWriter->renderJsonResponse($result->toArray(), $httpStatus);
    }

    private function initializeAppContext()
    {
        $appContext = new AppContext();
        $appContext->setShopUrl($this->environmentService->getShopUrl());
        $appContext->setDefaultShopId($this->environmentService->getDefaultShopId());
        $appContext->setDefaultShopLanguage($this->environmentService->getDefaultLanguage());
        try {
            $token = $this->tokenService->getToken($this->keyRegistry->getSignatureKey());
            $this->verifyToken($token);
            $appContext->setAuthToken($token);
        } catch (NoAuthHeaderException $e) { //pass
        }

        return $appContext;
    }

    /**
     * @param Token $token
     * @throws InvalidTokenException
     */
    private function verifyToken(Token $token):void
    {
        if ($token->getIssuer() !== $this->environmentService->getShopUrl()) {
            throw new InvalidTokenException('Token issuer is not correct!');
        }
        if ($token->getAudience() !== $this->environmentService->getShopUrl()) {
            throw new InvalidTokenException('Token audience is not correct!');
        }
        // We probably could also check if language and shopid are permitted,
        // but if not, the request will fail anyway some way further down the
        // line, so we leave this expensive check out.
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
    private function executeQuery(AppContext $context, $queryData)
    {
        $this->legacyWrapper->setLanguage($context->getCurrentLanguage());

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
            $context,
            $variables,
            $operationName
        );
        return $result;
    }
}
