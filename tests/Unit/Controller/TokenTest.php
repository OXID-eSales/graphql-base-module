<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Controller\Token as TokenController;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\Token;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\TokenAdministration;
use OxidEsales\GraphQL\Base\Service\TokenExceptionConverterInterface;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use TheCodingMachine\GraphQLite\Types\ID;

#[AllowMockObjectsWithoutExpectations]
class TokenTest extends BaseTestCase
{
    #[Test]
    public function tokensReturnsPayloadWithTokenList(): void
    {
        $userId = uniqid();
        $userDataType = $this->getUserDataStub($this->getUserModelStub($userId));
        $tokenList = [$this->createStub(Token::class)];

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')->willReturn($userDataType);

        $expectedFilter = new TokenFilterList(new IDFilter($userDataType->id()));
        $expectedPagination = new Pagination();
        $expectedSort = new TokenSorting(Sorting::SORTING_ASC);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('tokens')
            ->with($expectedFilter, $expectedPagination, $expectedSort)
            ->willReturn($tokenList);

        $sut = $this->getSut(authentication: $authenticationStub, tokenExceptionConverter: $converterMock);
        $payload = $sut->tokens();

        $this->assertSame($tokenList, $payload->tokens());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function tokensWithCustomParametersReturnsPayloadWithTokenList(): void
    {
        $tokenList = [$this->createStub(Token::class)];
        $filter = new TokenFilterList(new IDFilter(new ID(uniqid())));
        $pagination = Pagination::fromUserInput(10, 20);
        $sort = new TokenSorting(Sorting::SORTING_DESC);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('tokens')
            ->with($filter, $pagination, $sort)
            ->willReturn($tokenList);

        $sut = $this->getSut(tokenExceptionConverter: $converterMock);
        $payload = $sut->tokens($filter, $pagination, $sort);

        $this->assertSame($tokenList, $payload->tokens());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function tokensReturnsPayloadWithError(): void
    {
        $userDataType = $this->getUserDataStub($this->getUserModelStub(uniqid()));
        $errorStub = $this->createStub(ErrorInterface::class);

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')->willReturn($userDataType);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('tokens')->willReturn($errorStub);

        $sut = $this->getSut(authentication: $authenticationStub, tokenExceptionConverter: $converterMock);
        $payload = $sut->tokens();

        $this->assertNull($payload->tokens());
        $this->assertCount(1, $payload->userErrors());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    #[Test]
    public function refreshReturnsPayloadWithToken(): void
    {
        $refreshToken = uniqid();
        $fingerprintHash = uniqid();
        $tokenValue = uniqid();

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('refresh')
            ->with($refreshToken, $fingerprintHash)
            ->willReturn($this->createConfiguredStub(UnencryptedToken::class, ['toString' => $tokenValue]));

        $sut = $this->getSut(tokenExceptionConverter: $converterMock);
        $payload = $sut->refresh($refreshToken, $fingerprintHash);

        $this->assertSame($tokenValue, $payload->token());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function refreshReturnsPayloadWithError(): void
    {
        $refreshToken = uniqid();
        $fingerprintHash = uniqid();
        $errorStub = $this->createStub(ErrorInterface::class);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('refresh')
            ->with($refreshToken, $fingerprintHash)
            ->willReturn($errorStub);

        $sut = $this->getSut(tokenExceptionConverter: $converterMock);
        $payload = $sut->refresh($refreshToken, $fingerprintHash);

        $this->assertNull($payload->token());
        $this->assertCount(1, $payload->userErrors());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    #[Test]
    public function customerTokensDeleteReturnsPayloadWithDeleteCount(): void
    {
        $customerId = new ID(uniqid());
        $deleteCount = rand();

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('customerTokensDelete')
            ->with($customerId)
            ->willReturn($deleteCount);

        $sut = $this->getSut(tokenExceptionConverter: $converterMock);
        $payload = $sut->customerTokensDelete($customerId);

        $this->assertSame($deleteCount, $payload->deletedCount());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function customerTokensDeleteReturnsPayloadWithError(): void
    {
        $customerId = new ID(uniqid());
        $errorStub = $this->createStub(ErrorInterface::class);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('customerTokensDelete')
            ->with($customerId)
            ->willReturn($errorStub);

        $sut = $this->getSut(tokenExceptionConverter: $converterMock);
        $payload = $sut->customerTokensDelete($customerId);

        $this->assertNull($payload->deletedCount());
        $this->assertCount(1, $payload->userErrors());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    #[Test]
    public function tokenDeleteWithAdminRightReturnsPayload(): void
    {
        $tokenId = new ID(uniqid());

        $authorizationStub = $this->createStub(Authorization::class);
        $authorizationStub->method('isAllowed')->with('INVALIDATE_ANY_TOKEN')->willReturn(true);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('deleteToken')
            ->with($tokenId)
            ->willReturn(true);

        $sut = $this->getSut(authorization: $authorizationStub, tokenExceptionConverter: $converterMock);
        $payload = $sut->tokenDelete($tokenId);

        $this->assertSame(1, $payload->deletedCount());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function tokenDeleteWithAdminRightReturnsPayloadWithError(): void
    {
        $tokenId = new ID(uniqid());
        $errorStub = $this->createStub(ErrorInterface::class);

        $authorizationStub = $this->createStub(Authorization::class);
        $authorizationStub->method('isAllowed')->with('INVALIDATE_ANY_TOKEN')->willReturn(true);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('deleteToken')
            ->with($tokenId)
            ->willReturn($errorStub);

        $sut = $this->getSut(authorization: $authorizationStub, tokenExceptionConverter: $converterMock);
        $payload = $sut->tokenDelete($tokenId);

        $this->assertNull($payload->deletedCount());
        $this->assertCount(1, $payload->userErrors());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    #[Test]
    public function tokenDeleteWithoutAdminRightReturnsPayload(): void
    {
        $tokenId = new ID(uniqid());
        $userDataType = $this->getUserDataStub($this->getUserModelStub(uniqid()));

        $authorizationStub = $this->createStub(Authorization::class);
        $authorizationStub->method('isAllowed')->with('INVALIDATE_ANY_TOKEN')->willReturn(false);

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')->willReturn($userDataType);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('deleteUserToken')
            ->with($userDataType, $tokenId)
            ->willReturn(true);

        $sut = $this->getSut(
            authentication: $authenticationStub,
            authorization: $authorizationStub,
            tokenExceptionConverter: $converterMock
        );
        $payload = $sut->tokenDelete($tokenId);

        $this->assertSame(1, $payload->deletedCount());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function tokenDeleteWithoutAdminRightReturnsPayloadWithError(): void
    {
        $tokenId = new ID(uniqid());
        $userDataType = $this->getUserDataStub($this->getUserModelStub(uniqid()));
        $errorStub = $this->createStub(ErrorInterface::class);

        $authorizationStub = $this->createStub(Authorization::class);
        $authorizationStub->method('isAllowed')->with('INVALIDATE_ANY_TOKEN')->willReturn(false);

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')->willReturn($userDataType);

        $converterMock = $this->createMock(TokenExceptionConverterInterface::class);
        $converterMock->method('deleteUserToken')
            ->with($userDataType, $tokenId)
            ->willReturn($errorStub);

        $sut = $this->getSut(
            authentication: $authenticationStub,
            authorization: $authorizationStub,
            tokenExceptionConverter: $converterMock
        );
        $payload = $sut->tokenDelete($tokenId);

        $this->assertNull($payload->deletedCount());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    #[Test]
    public function shopTokensDeleteReturnsPayload(): void
    {
        $deleteCount = rand();

        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('shopTokensDelete')->willReturn($deleteCount);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationStub);
        $payload = $sut->shopTokensDelete();

        $this->assertSame($deleteCount, $payload->deletedCount());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function regenerateSignatureKeyReturnsPayload(): void
    {
        $success = (bool)rand(0, 1);

        $tokenAdministrationStub = $this->createStub(TokenAdministration::class);
        $tokenAdministrationStub->method('regenerateSignatureKey')->willReturn($success);

        $sut = $this->getSut(tokenAdministration: $tokenAdministrationStub);
        $payload = $sut->regenerateSignatureKey();

        $this->assertSame($success, $payload->success());
        $this->assertEmpty($payload->userErrors());
    }

    private function getSut(
        ?TokenAdministration $tokenAdministration = null,
        ?Authentication $authentication = null,
        ?Authorization $authorization = null,
        ?TokenExceptionConverterInterface $tokenExceptionConverter = null,
    ): TokenController {
        return new TokenController(
            tokenAdministration: $tokenAdministration ?? $this->createStub(TokenAdministration::class),
            authentication: $authentication ?? $this->createStub(Authentication::class),
            authorization: $authorization ?? $this->createStub(Authorization::class),
            tokenExceptionConverter: $tokenExceptionConverter ?? $this->createStub(
                TokenExceptionConverterInterface::class
            ),
        );
    }
}
