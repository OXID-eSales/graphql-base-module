<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Framework;

use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Framework\ErrorCodeProvider;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandler;
use OxidEsales\GraphQl\Framework\RequestReaderInterface;
use OxidEsales\GraphQl\Framework\ResponseWriterInterface;
use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Framework\TypeFactory;
use OxidEsales\GraphQl\Service\AuthenticationService;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistry;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Type\LoginType;
use OxidEsales\GraphQl\Type\Mutation;
use OxidEsales\GraphQl\Type\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GraphQlQueryHandlerTest extends TestCase
{
    const SIGNATURE_KEY = '1234567890123456';

    /** @var RequestReaderInterface|MockObject */
    private $requestReader;

    /** @var  ResponseWriterInterface|MockObject */
    private $responseWriter;

    /** @var  UserDaoInterface|MockObject */
    private $userDao;

    /** @var  KeyRegistry|MockObject */
    private $keyRegistry;

    /** @var  EnvironmentServiceInterface|MockObject */
    private $environmentService;

    /** @var  GraphQlQueryHandler */
    private $graphQlQueryHandler;

    private $result;

    private $httpStatus;

    public function setUp()
    {

        $this->requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $this->responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $this->responseWriter->method('renderJsonResponse')->willReturnCallback([$this, 'renderResponse']);
        $this->userDao = $this->getMockBuilder(UserDaoInterface::class)->getMock();
        $this->keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $this->keyRegistry->method('getSignatureKey')->willReturn($this::SIGNATURE_KEY);
        $this->environmentService = $this->getMockBuilder(EnvironmentServiceInterface::class)->getMock();
        $this->environmentService->method('getShopUrl')->willReturn('http://myshop.com');
        $this->environmentService->method('getDefaultLanguage')->willReturn('de');
        $this->environmentService->method('getDefaultShopId')->willReturn('1');

        $loginType = new LoginType(
            new AuthenticationService($this->environmentService, $this->userDao),
            $this->keyRegistry);
        $queryTypeFactory = new TypeFactory(Query::class);
        $queryTypeFactory->addSubType($loginType);
        $schemaFactory = new SchemaFactory($queryTypeFactory, new TypeFactory(Mutation::class));

        $this->graphQlQueryHandler = new GraphQlQueryHandler(
            new NullLogger(),
            $this->environmentService,
            $this->keyRegistry,
            $schemaFactory,
            new ErrorCodeProvider(),
            $this->requestReader,
            $this->responseWriter);
    }

    public function renderResponse($result, $httpStatus)
    {
        $this->result = $result;
        $this->httpStatus = $httpStatus;
    }

    public function testTokenSigatureError()
    {
        $token = $this->initializeToken();
        $jwt = $token->getJwt('1111111111111111');
        $this->requestReader->method('getAuthorizationHeader')->willReturn("Bearer $jwt");

        $this->graphQlQueryHandler->executeGraphQlQuery();
        $this->assertErrorMessage('/Signature verification failed/');
        $this->assertHttpStatus(401);
    }

    public function testTokenExpired()
    {
        $token = $this->initializeToken(-7);
        $jwt = $token->getJwt($this::SIGNATURE_KEY);
        $this->requestReader->method('getAuthorizationHeader')->willReturn("Bearer $jwt");

        $this->graphQlQueryHandler->executeGraphQlQuery();
        $this->assertErrorMessage('/Expired token/');
        $this->assertHttpStatus(401);
    }

    public function testIssuerDoesNotMatch()
    {
        $token = $this->initializeToken();
        $token->setShopUrl('http://anothershop.com');
        $jwt = $token->getJwt($this::SIGNATURE_KEY);
        $this->requestReader->method('getAuthorizationHeader')->willReturn("Bearer $jwt");

        $this->graphQlQueryHandler->executeGraphQlQuery();
        $this->assertErrorMessage('/Token issuer is not correct!/');
        $this->assertHttpStatus(401);
    }

    public function testAudienceDoesNotMatch()
    {
        # The public Token interface does not allow to set issuer and audience separately
        $jwtProperty = new \ReflectionProperty(Token::class, 'jwtObject');
        $jwtProperty->setAccessible(true);
        $token = $this->initializeToken();
        $jwtObject = $jwtProperty->getValue($token);
        $jwtObject->aud = 'http://anothershop.com';
        $jwtProperty->setValue($token, $jwtObject);

        $jwt = $token->getJwt($this::SIGNATURE_KEY);
        $this->requestReader->method('getAuthorizationHeader')->willReturn("Bearer $jwt");

        $this->graphQlQueryHandler->executeGraphQlQuery();

        $this->assertErrorMessage('/Token audience is not correct/');
        $this->assertHttpStatus(401);

    }

    public function testUserLogin()
    {
        $this->userDao->method('fetchUserGroup')->willReturn('customer');
        $query = <<< EOQ
query TestLogin {
    login (username: "someuser", password: "password", lang: "en", shopid: 25) {
        token       
    }
}
EOQ;
        $this->requestReader->method('getAuthorizationHeader')
            ->willThrowException(new NoAuthHeaderException());
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);

        $this->graphQlQueryHandler->executeGraphQlQuery();


        $jwt = $this->result['data']['login']['token'];
        $this->assertNotNull($jwt);
        $token = new Token();
        $token->setJwt($jwt, $this::SIGNATURE_KEY);
        $this->assertEquals('someuser', $token->getSubject());
    }

    public function testAnonymousLogin()
    {
        $this->userDao->method('fetchUserGroup')->willReturn('customer');
        $query = <<< EOQ
query TestLogin {
    login {
        token       
    }
}
EOQ;
        $this->requestReader->method('getAuthorizationHeader')
            ->willThrowException(new NoAuthHeaderException());
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);

        $this->graphQlQueryHandler->executeGraphQlQuery();


        $jwt = $this->result['data']['login']['token'];
        $this->assertNotNull($jwt);
        $token = new Token();
        $token->setJwt($jwt, $this::SIGNATURE_KEY);
        $this->assertEquals('anonymous', $token->getSubject());
        $this->assertEquals('anonymous', $token->getUserGroup());
    }

    public function testGraphQlSyntaxError()
    {
        $this->userDao->method('fetchUserGroup')->willReturn('customer');
        $query = <<< EOQ
query bla TestLogin {
    login {
        token       
    }
}
EOQ;
        $this->requestReader->method('getAuthorizationHeader')
            ->willThrowException(new NoAuthHeaderException());
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);

        $this->graphQlQueryHandler->executeGraphQlQuery();

        $this->assertErrorMessage('/.*Syntax Error.*/');

    }

    public function testGraphQlMissingTypeError()
    {
        $this->userDao->method('fetchUserGroup')->willReturn('customer');
        $query = <<< EOQ
query TestLogin {
    logout {
        token       
    }
}
EOQ;
        $this->requestReader->method('getAuthorizationHeader')
            ->willThrowException(new NoAuthHeaderException());
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);

        $this->graphQlQueryHandler->executeGraphQlQuery();

        $this->assertErrorMessage('/.*Cannot query field.*/');

    }

    public function testUnknownError()
    {
        $this->requestReader->method('getAuthorizationHeader')
            ->willThrowException(new \Exception());

        $this->graphQlQueryHandler->executeGraphQlQuery();
        $this->assertErrorMessage('/Unknown error:.*/');
    }

    private function assertErrorMessage(string $regex)
    {
        $error = $this->result['errors'][0]['message'];
        $this->assertRegExp($regex, $error);
    }

    private function assertHttpStatus(int $httpStatus)
    {
        $this->assertEquals($httpStatus, $this->httpStatus);
    }

    private function initializeToken($expiryDays = 7): Token
    {
        $token = new Token($expiryDays);
        $token->setSubject('My Subject');
        $token->setShopUrl('http://myshop.com');
        $token->setShopid(1);
        $token->setUserGroup('customer');
        $token->setLang('de');
        return $token;
    }

}
