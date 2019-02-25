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
use OxidEsales\GraphQl\Exception\InvalidTokenException;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use Psr\Log\LoggerInterface;

class GraphQlQueryHandler implements GraphQlQueryHandlerInterface
{

    /** @var LoggerInterface  */
    private $logger;
    /** @var EnvironmentServiceInterface  */
    private $environmentService;
    /** @var KeyRegistryInterface */
    private $keyRegistry;
    /** @var SchemaFactoryInterface  */
    private $schemaFactory;
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
        KeyRegistryInterface $keyRegistry,
        SchemaFactoryInterface $schemaFactory,
        ErrorCodeProviderInterface $errorCodeProvider,
        RequestReaderInterface $requestReader,
        ResponseWriterInterface $responseWriter
    )
    {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
        $this->keyRegistry = $keyRegistry;
        $this->schemaFactory = $schemaFactory;
        $this->errorCodeProvider = $errorCodeProvider;
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;

        $this->loggingErrorFormatter = function(Error $error) use (&$httpStatus) {
            $this->logger->error($error);
            $httpStatus = $this->errorCodeProvider->getHttpReturnCode($error);
            return FormattedError::createFromException($error);
        };

    }

    public function executeGraphQlQuery()
    {
        try {
            $context = $this->initializeAppContext();
            $queryData = $this->requestReader->getGraphQLRequestData();
            $result = $this->executeQuery($context, $queryData);
        } catch (\Exception $e)
        {
            $result = $this->createErrorResult($e);
        }
        $result->setErrorFormatter($this->loggingErrorFormatter);
        $this->responseWriter->renderJsonResponse($result->toArray(), 401);

    }

    private function initializeAppContext()
    {
        $appContext = new AppContext();
        $appContext->setShopUrl($this->environmentService->getShopUrl());
        $appContext->setDefaultShopId($this->environmentService->getDefaultShopId());
        $appContext->setDefaultShopLanguage($this->environmentService->getDefaultLanguage());
        try {
            $jwt = $this->getAuthTokenString();
            $token = new Token();
            // This checks that the auth token is valid, i.e. untampered
            // and valid
            $token->setJwt($jwt, $this->keyRegistry->getSignatureKey());
            $this->verifyToken($token);
            $appContext->setAuthToken($token);
        }
        catch (NoAuthHeaderException $e)
        {
            // pass
        }
        return $appContext;
    }

    private function verifyToken(Token $token)
    {
        if ($token->getIssuer() !== $this->environmentService->getShopUrl())
        {
            throw new InvalidTokenException('Token issuer is not correct!');
        }
        if ($token->getAudience() !== $this->environmentService->getShopUrl())
        {
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

    private function getAuthTokenString()
    {
        $authHeader = $this->requestReader->getAuthorizationHeader();
        if (! $authHeader) {
            throw new NoAuthHeaderException();
        }
        list($jwt) = sscanf( $authHeader, 'Bearer %s');
        return $jwt;
    }

    /**
     * Execute the GraphQL query
     *
     * @throws \Throwable
     */
    private function executeQuery(AppContext $context, $queryData)
    {
        $graphQL = new \GraphQL\GraphQL();
        $result = $graphQL->executeQuery(
            $this->schemaFactory->getSchema(),
            $queryData['query'],
            null,
            $context,
            (array) $queryData['variables'],
            $queryData['operationName']
        );
        return $result;
    }

}
