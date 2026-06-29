<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use OxidEsales\GraphQL\Base\Controller\Token as TokenController;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\AuthorizationError;
use OxidEsales\GraphQL\Base\DataType\Error\NotFoundError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\Filter\DateFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\TokenPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\TokensPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidRefreshToken;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Exception\UnknownToken;
use OxidEsales\GraphQL\Base\Exception\UserNotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\RefreshTokenServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenAdministration as TokenAdministration;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use TheCodingMachine\GraphQLite\Types\ID;

//todo: tests do not do any assertions, fix it.
#[AllowMockObjectsWithoutExpectations]
class TokenTest extends BaseTestCase
{
    public function testTokensQueryWithDefaultFilters(): void
    {
        $authentication = $this->createPartialMock(Authentication::class, ['getUser']);
        $authentication->method('getUser')
            ->willReturn(new UserDataType($this->getUserModelStub('_testuserid')));

        $tokenAdministration = $this->createPartialMock(TokenAdministration::class, ['tokens']);
        $tokenAdministration->method('tokens')
            ->with(
                new TokenFilterList(new IDFilter($authentication->getUser()->id())),
                new Pagination(),
                new TokenSorting(TokenSorting::SORTING_ASC),
            )
            ->willReturn($payloadStub = $this->createStub(TokensPayloadInterface::class));

        $tokenController = $this->getTokenController(
            tokenAdministration: $tokenAdministration,
            authentication: $authentication
        );
        $this->assertSame($payloadStub, $tokenController->tokens());
    }

    public function testTokensQueryWithCustomFilters(): void
    {
        $authentication = $this->createPartialMock(Authentication::class, ['getUser']);
        $authentication->method('getUser')
            ->willReturn(new UserDataType($this->getUserModelStub('_testuserid')));

        $filterList = new TokenFilterList(
            new IDFilter(new ID('someone_else')),
            new IDFilter(new ID(1)),
            new DateFilter(null, ['2021-01-12 12:12:12', '2021-12-31 12:12:12'])
        );
        $sort = new TokenSorting(TokenSorting::SORTING_DESC);
        $pagination = Pagination::fromUserInput(10, 20);

        $tokenAdministration = $this->createPartialMock(TokenAdministration::class, ['tokens']);
        $tokenAdministration->method('tokens')
            ->with(
                $filterList,
                $pagination,
                $sort
            )
            ->willReturn($payloadStub = $this->createStub(TokensPayloadInterface::class));

        $tokenController = $this->getTokenController(
            tokenAdministration: $tokenAdministration,
            authentication: $authentication
        );
        $this->assertSame($payloadStub, $tokenController->tokens($filterList, $pagination, $sort));
    }

    public function testCustomerTokensDelete(): void
    {
        $authentication = $this->createPartialMock(Authentication::class, []);
        $tokenAdministration = $this->createPartialMock(TokenAdministration::class, ['customerTokensDelete']);
        $tokenAdministration->method('customerTokensDelete')
            ->willReturn(5);

        $tokenController = $this->getTokenController(
            tokenAdministration: $tokenAdministration,
            authentication: $authentication
        );
        $tokenController->customerTokensDelete(new ID('someUserId'));
    }

    public function testCustomerTokensDeleteWithInvalidLoginException(): void
    {
        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('customerTokensDelete')->willThrowException(new InvalidLogin(uniqid()));

        $sut = $this->getTokenController(tokenAdministration: $tokenAdministrationStub);
        $payload = $sut->customerTokensDelete(new ID(uniqid()));
        $expectedError = AuthorizationError::fromCode(AuthorizationError::UNAUTHORIZED_DELETE_TOKEN);

        $this->assertNull($payload->deletedCount());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    public function testCustomerTokensDeleteWithUserNotFoundException(): void
    {
        $customerId = uniqid();
        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('customerTokensDelete')->willThrowException(new UserNotFound($customerId));

        $sut = $this->getTokenController(tokenAdministration: $tokenAdministrationStub);
        $payload = $sut->customerTokensDelete(new ID($customerId));
        $expectedError = NotFoundError::fromCode(NotFoundError::NOT_FOUND_USER, $customerId);

        $this->assertNull($payload->deletedCount());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    public function testTokenDelete(): void
    {
        $authorization = $this->createPartialMock(Authorization::class, ['isAllowed']);
        $authorization->method('isAllowed')
            ->willReturn(true);

        $tokenController = $this->getTokenController(
            authorization: $authorization
        );
        $tokenController->tokenDelete(new ID('someTokenId'));
    }

    public function testTokenDeleteWithUnknownTokenException(): void
    {
        $tokenId = uniqid();

        $tokenServiceStub = $this->createStub(TokenService::class);
        $tokenServiceStub->method('deleteToken')->willThrowException(new UnknownToken());

        $authorizationStub = $this->createPartialMock(Authorization::class, ['isAllowed']);
        $authorizationStub->method('isAllowed')->willReturn(true);

        $sut = $this->getTokenController(tokenService: $tokenServiceStub, authorization: $authorizationStub);
        $payload = $sut->tokenDelete(new ID($tokenId));
        $expectedError = NotFoundError::fromCode(NotFoundError::NOT_FOUND_TOKEN, $tokenId);

        $this->assertNull($payload->deletedCount());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    public function testRefreshReturnsTokenPayload(): void
    {
        $sut = $this->getTokenController(
            refreshTokenService: $refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class),
        );

        $refreshToken = uniqid();
        $fingerprintHash = uniqid();
        $payloadStub = $this->createStub(TokenPayloadInterface::class);

        $refreshTokenServiceMock->method('refreshToken')
            ->with($refreshToken, $fingerprintHash)->willReturn($payloadStub);

        $this->assertSame($payloadStub, $sut->refresh($refreshToken, $fingerprintHash));
    }

    public function testTokensWithInvalidLoginException(): void
    {
        $authenticationStub = $this->createPartialMock(Authentication::class, ['getUser']);
        $authenticationStub->method('getUser')->willReturn(new UserDataType($this->getUserModelStub(uniqid())));

        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('tokens')->willThrowException(new InvalidLogin(uniqid()));

        $sut = $this->getTokenController(
            tokenAdministration: $tokenAdministrationStub,
            authentication: $authenticationStub
        );
        $payload = $sut->tokens();
        $expectedError = AuthorizationError::fromCode(AuthorizationError::UNAUTHORIZED_VIEW_TOKEN);

        $this->assertEmpty($payload->tokens());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    public function testRefreshWithFingerprintValidationException(): void
    {
        $refreshTokenServiceStub = $this->createStub(RefreshTokenServiceInterface::class);
        $refreshTokenServiceStub->method('refreshToken')->willThrowException(
            new FingerprintValidationException(uniqid())
        );

        $sut = $this->getTokenController(refreshTokenService: $refreshTokenServiceStub);
        $payload = $sut->refresh(uniqid(), uniqid());
        $expectedError = ValidationError::fromCode(ValidationError::INVALID_FINGERPRINT);

        $this->assertNull($payload->token());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    public function testRefreshWithInvalidRefreshTokenException(): void
    {
        $refreshTokenServiceStub = $this->createStub(RefreshTokenServiceInterface::class);
        $refreshTokenServiceStub->method('refreshToken')->willThrowException(new InvalidRefreshToken(uniqid()));

        $sut = $this->getTokenController(refreshTokenService: $refreshTokenServiceStub);
        $payload = $sut->refresh(uniqid(), uniqid());
        $expectedError = ValidationError::fromCode(ValidationError::INVALID_REFRESH_TOKEN);

        $this->assertNull($payload->token());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    public function testRefreshWithTokenQuotaExceededException(): void
    {
        $refreshTokenServiceStub = $this->createStub(RefreshTokenServiceInterface::class);
        $refreshTokenServiceStub->method('refreshToken')->willThrowException(new TokenQuota(uniqid()));

        $sut = $this->getTokenController(refreshTokenService: $refreshTokenServiceStub);
        $payload = $sut->refresh(uniqid(), uniqid());
        $expectedError = AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED);

        $this->assertNull($payload->token());
        $this->assertCount(1, $payload->userErrors());
        $this->assertEquals($expectedError, $payload->userErrors()[0]);
    }

    private function getTokenController(
        ?TokenAdministration $tokenAdministration = null,
        ?Authentication $authentication = null,
        ?Authorization $authorization = null,
        ?TokenService $tokenService = null,
        ?RefreshTokenServiceInterface $refreshTokenService = null,
    ): TokenController {
        return new TokenController(
            tokenAdministration: $tokenAdministration ?? $this->createStub(TokenAdministration::class),
            authentication: $authentication ?? $this->createStub(Authentication::class),
            authorization: $authorization ?? $this->createStub(Authorization::class),
            tokenService: $tokenService ?? $this->createStub(TokenService::class),
            refreshTokenService: $refreshTokenService ?? $this->createStub(RefreshTokenServiceInterface::class),
        );
    }
}
