<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace ExceptionConverter;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\AuthorizationError;
use OxidEsales\GraphQL\Base\DataType\Error\NotFoundError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\Token as TokenDataType;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidRefreshToken;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Exception\UnknownToken;
use OxidEsales\GraphQL\Base\Exception\UserNotFound;
use OxidEsales\GraphQL\Base\ExceptionConverter\TokenExceptionConverter;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use OxidEsales\GraphQL\Base\Service\RefreshTokenServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenAdministration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

class TokenExceptionConverterTest extends TestCase
{
    #[Test]
    public function tokensReturnsTokenListOnSuccess(): void
    {
        $tokenList = [new TokenDataType($this->createStub(TokenModel::class))];
        $filterList = new TokenFilterList();
        $pagination = new Pagination();
        $sort = new TokenSorting(TokenSorting::SORTING_ASC);

        $tokenAdministrationMock = $this->createMock(TokenAdministration::class);
        $tokenAdministrationMock->method('tokens')->with($filterList, $pagination, $sort)->willReturn($tokenList);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationMock);

        $this->assertSame($tokenList, $sut->tokens($filterList, $pagination, $sort));
    }

    #[Test]
    public function tokensReturnsAuthorizationErrorOnInvalidLogin(): void
    {
        $filterList = new TokenFilterList();
        $pagination = new Pagination();
        $sort = new TokenSorting(TokenSorting::SORTING_ASC);

        $tokenAdministrationMock = $this->createStub(TokenAdministration::class);
        $tokenAdministrationMock->method('tokens')->willThrowException(new InvalidLogin(uniqid()));

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationMock);
        $result = $sut->tokens($filterList, $pagination, $sort);

        $this->assertEquals(AuthorizationError::fromCode(AuthorizationError::UNAUTHORIZED_VIEW_TOKEN), $result);
    }

    #[Test]
    public function refreshReturnsTokenOnSuccess(): void
    {
        $refreshToken = uniqid();
        $fingerprintHash = uniqid();
        $tokenStub = $this->createStub(UnencryptedToken::class);

        $refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $refreshTokenServiceMock->method('refreshToken')->with($refreshToken, $fingerprintHash)->willReturn($tokenStub);

        $sut = $this->getSut(refreshTokenService: $refreshTokenServiceMock);

        $this->assertSame($tokenStub, $sut->refresh($refreshToken, $fingerprintHash));
    }

    #[Test]
    public function refreshReturnsValidationErrorOnFingerprintValidationException(): void
    {
        $refreshToken = uniqid();
        $fingerprintHash = uniqid();

        $refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $refreshTokenServiceMock->method('refreshToken')
            ->with($refreshToken, $fingerprintHash)
            ->willThrowException(new FingerprintValidationException(uniqid()));

        $sut = $this->getSut(refreshTokenService: $refreshTokenServiceMock);
        $result = $sut->refresh($refreshToken, $fingerprintHash);

        $this->assertEquals(ValidationError::fromCode(ValidationError::FINGERPRINT, $fingerprintHash), $result);
    }

    #[Test]
    public function refreshReturnsValidationErrorOnInvalidRefreshToken(): void
    {
        $refreshToken = uniqid();
        $fingerprintHash = uniqid();

        $refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $refreshTokenServiceMock->method('refreshToken')
            ->with($refreshToken, $fingerprintHash)
            ->willThrowException(new InvalidRefreshToken(uniqid()));

        $sut = $this->getSut(refreshTokenService: $refreshTokenServiceMock);
        $result = $sut->refresh($refreshToken, $fingerprintHash);

        $this->assertEquals(ValidationError::fromCode(ValidationError::REFRESH_TOKEN, $refreshToken), $result);
    }

    #[Test]
    public function refreshReturnsAuthenticationErrorOnTokenQuota(): void
    {
        $refreshToken = uniqid();
        $fingerprintHash = uniqid();

        $refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $refreshTokenServiceMock->method('refreshToken')
            ->with($refreshToken, $fingerprintHash)
            ->willThrowException(new TokenQuota(uniqid()));

        $sut = $this->getSut(refreshTokenService: $refreshTokenServiceMock);
        $result = $sut->refresh($refreshToken, $fingerprintHash);

        $this->assertEquals(AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED), $result);
    }

    #[Test]
    public function customerTokensDeleteReturnsDeleteCountOnSuccess(): void
    {
        $deleteCount = rand();
        $customerId = new ID(uniqid());

        $tokenAdministrationMock = $this->createMock(TokenAdministration::class);
        $tokenAdministrationMock->method('customerTokensDelete')->with($customerId)->willReturn($deleteCount);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationMock);

        $this->assertSame($deleteCount, $sut->customerTokensDelete($customerId));
    }

    #[Test]
    public function customerTokensDeleteReturnsDeleteCountOnSuccessWithNull(): void
    {
        $deleteCount = rand();
        $customerId = null;

        $tokenAdministrationMock = $this->createMock(TokenAdministration::class);
        $tokenAdministrationMock->method('customerTokensDelete')->with($customerId)->willReturn($deleteCount);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationMock);

        $this->assertSame($deleteCount, $sut->customerTokensDelete($customerId));
    }

    #[Test]
    public function customerTokensDeleteReturnsAuthorizationErrorOnInvalidLogin(): void
    {
        $customerId = new ID(uniqid());

        $tokenAdministrationMock = $this->createMock(TokenAdministration::class);
        $tokenAdministrationMock->method('customerTokensDelete')
            ->with($customerId)
            ->willThrowException(new InvalidLogin(uniqid()));

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationMock);
        $result = $sut->customerTokensDelete($customerId);

        $this->assertEquals(AuthorizationError::fromCode(AuthorizationError::UNAUTHORIZED_DELETE_TOKEN), $result);
    }

    #[Test]
    public function customerTokensDeleteReturnsNotFoundErrorOnUserNotFound(): void
    {
        $customerId = new ID(uniqid());

        $tokenAdministrationMock = $this->createMock(TokenAdministration::class);
        $tokenAdministrationMock->method('customerTokensDelete')
            ->with($customerId)
            ->willThrowException(new UserNotFound((string)$customerId));

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationMock);
        $result = $sut->customerTokensDelete($customerId);

        $this->assertEquals(
            NotFoundError::fromCode(NotFoundError::USER, (string)$customerId),
            $result
        );
    }

    #[Test]
    public function deleteTokenReturnsTrueOnSuccess(): void
    {
        $tokenId = new ID(uniqid());

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->expects($this->once())->method('deleteToken')->with($tokenId);

        $sut = $this->getSut(tokenService: $tokenServiceMock);

        $this->assertTrue($sut->deleteToken($tokenId));
    }

    #[Test]
    public function deleteTokenReturnsNotFoundErrorOnUnknownToken(): void
    {
        $tokenId = new ID(uniqid());

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->method('deleteToken')->with($tokenId)->willThrowException(new UnknownToken());

        $sut = $this->getSut(tokenService: $tokenServiceMock);
        $result = $sut->deleteToken($tokenId);

        $this->assertEquals(
            NotFoundError::fromCode(NotFoundError::TOKEN, (string)$tokenId),
            $result
        );
    }

    #[Test]
    public function deleteUserTokenReturnsTrueOnSuccess(): void
    {
        $userStub = $this->createStub(UserInterface::class);
        $tokenId = new ID(uniqid());

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->expects($this->once())->method('deleteUserToken')->with($userStub, $tokenId);

        $sut = $this->getSut(tokenService: $tokenServiceMock);

        $this->assertTrue($sut->deleteUserToken($userStub, $tokenId));
    }

    #[Test]
    public function deleteUserTokenReturnsNotFoundErrorOnUnknownToken(): void
    {
        $userStub = $this->createStub(UserInterface::class);
        $tokenId = new ID(uniqid());

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->method('deleteUserToken')->with($userStub, $tokenId)->willThrowException(new UnknownToken());

        $sut = $this->getSut(tokenService: $tokenServiceMock);
        $result = $sut->deleteUserToken($this->createStub(UserInterface::class), $tokenId);

        $this->assertEquals(
            NotFoundError::fromCode(NotFoundError::TOKEN, (string)$tokenId),
            $result
        );
    }

    #[Test]
    public function shopTokensDeleteReturnsDeleteCount(): void
    {
        $deleteCount = rand();

        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('shopTokensDelete')->willReturn($deleteCount);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationStub);

        $this->assertSame($deleteCount, $sut->shopTokensDelete());
    }

    #[Test]
    public function regenerateSignatureKeyReturnsSuccessFlag(): void
    {
        $success = (bool)rand(0, 1);

        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('regenerateSignatureKey')->willReturn($success);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationStub);

        $this->assertSame($success, $sut->regenerateSignatureKey());
    }

    private function getSut(
        ?TokenAdministration $tokenAdministration = null,
        ?TokenService $tokenService = null,
        ?RefreshTokenServiceInterface $refreshTokenService = null,
    ): TokenExceptionConverter {
        return new TokenExceptionConverter(
            $tokenAdministration ?? $this->createStub(TokenAdministration::class),
            $tokenService ?? $this->createStub(TokenService::class),
            $refreshTokenService ?? $this->createStub(RefreshTokenServiceInterface::class),
        );
    }
}
