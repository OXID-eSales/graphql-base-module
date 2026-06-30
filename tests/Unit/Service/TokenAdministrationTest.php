<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\Token as TokenDataType;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyInfrastructure;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use OxidEsales\GraphQL\Base\Infrastructure\ModuleSetup;
use OxidEsales\GraphQL\Base\Infrastructure\Repository as BaseRepository;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\TokenAdministration;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use TheCodingMachine\GraphQLite\Types\ID;

#[AllowMockObjectsWithoutExpectations]
class TokenAdministrationTest extends BaseTestCase
{
    #[Test]
    public function tokensReturnsListWhenViewAnyTokenRight(): void
    {
        $tokenList = [new TokenDataType($this->createStub(TokenModel::class))];
        $filterList = new TokenFilterList(customerId: new IDFilter(new ID(uniqid())));
        $pagination = new Pagination();
        $sort = new TokenSorting();

        $repositoryMock = $this->createMock(BaseRepository::class);
        $repositoryMock->method('getList')
            ->with(TokenDataType::class, $filterList, $pagination, $sort)
            ->willReturn($tokenList);

        $authorizationMock = $this->createMock(Authorization::class);
        $authorizationMock->method('isAllowed')->with('VIEW_ANY_TOKEN')->willReturn(true);

        $sut = $this->getSut(repository: $repositoryMock, authorization: $authorizationMock);

        $this->assertSame($tokenList, $sut->tokens($filterList, $pagination, $sort));
    }

    #[Test]
    public function tokensReturnsListWithoutCustomerIdFilter(): void
    {
        $tokenList = [new TokenDataType($this->createStub(TokenModel::class))];
        $filterList = new TokenFilterList();
        $pagination = new Pagination();
        $sort = new TokenSorting();

        $repositoryMock = $this->createMock(BaseRepository::class);
        $repositoryMock->method('getList')
            ->with(TokenDataType::class, $filterList, $pagination, $sort)
            ->willReturn($tokenList);

        $authorizationMock = $this->createMock(Authorization::class);
        $authorizationMock->method('isAllowed')->with('VIEW_ANY_TOKEN')->willReturn(false);

        $sut = $this->getSut(repository: $repositoryMock, authorization: $authorizationMock);

        $this->assertSame($tokenList, $sut->tokens($filterList, $pagination, $sort));
    }

    #[Test]
    public function tokensReturnsListWithFittingCustomerIdFilter(): void
    {
        $userId = uniqid();
        $tokenList = [new TokenDataType($this->createStub(TokenModel::class))];
        $filterList = new TokenFilterList(customerId: new IDFilter(new ID($userId)));
        $pagination = new Pagination();
        $sort = new TokenSorting();

        $repositoryMock = $this->createMock(BaseRepository::class);
        $repositoryMock->method('getList')
            ->with(TokenDataType::class, $filterList, $pagination, $sort)
            ->willReturn($tokenList);

        $authorizationMock = $this->createMock(Authorization::class);
        $authorizationMock->method('isAllowed')->with('VIEW_ANY_TOKEN')->willReturn(false);

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')
            ->willReturn(new UserDataType($this->getUserModelStub($userId)));

        $sut = $this->getSut(
            repository: $repositoryMock,
            authorization: $authorizationMock,
            authentication: $authenticationStub,
        );

        $this->assertSame($tokenList, $sut->tokens($filterList, $pagination, $sort));
    }

    #[Test]
    public function tokensThrowsInvalidLoginWhenFilterOnForeignCustomerId(): void
    {
        $filterList = new TokenFilterList(customerId: new IDFilter(new ID(uniqid())));

        $authorizationMock = $this->createMock(Authorization::class);
        $authorizationMock->method('isAllowed')->with('VIEW_ANY_TOKEN')->willReturn(false);

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')
            ->willReturn(new UserDataType($this->getUserModelStub(uniqid())));

        $sut = $this->getSut(authorization: $authorizationMock, authentication: $authenticationStub);

        $this->expectExceptionObject(new InvalidLogin('Unauthorized'));
        $sut->tokens($filterList, new Pagination(), new TokenSorting());
    }

    public function testCustomerTokensDeleteOwnByDefault(): void
    {
        $userDataType = new UserDataType($this->getUserModelStub('_testuserid'));

        $repositoryMock = $this->createPartialMock(BaseRepository::class, ['getById']);
        $repositoryMock->method('getById')->willReturn($userDataType);

        $authorizationService = $this->createPartialMock(Authorization::class, ['isAllowed']);
        $authorizationService->method('isAllowed')->willReturn(false);

        $authenticationService = $this->createPartialMock(Authentication::class, ['getUser']);
        $authenticationService->method('getUser')->willReturn($userDataType);

        $tokenInfrastructure = $this->createPartialMock(TokenInfrastructure::class, ['tokenDelete']);
        $tokenInfrastructure->method('tokenDelete')->with($userDataType)->willReturn(5);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authorization: $authorizationService,
            authentication: $authenticationService,
            tokenInfrastructure: $tokenInfrastructure,
        );

        $this->assertEquals(5, $sut->customerTokensDelete(null));
    }

    public function testCustomerTokensDeleteOwnWithId(): void
    {
        $userDataType = new UserDataType($this->getUserModelStub('_testuserid'));

        $repositoryMock = $this->createPartialMock(BaseRepository::class, ['getById']);
        $repositoryMock->method('getById')->willReturn($userDataType);

        $authorizationService = $this->createPartialMock(Authorization::class, ['isAllowed']);
        $authorizationService->method('isAllowed')->willReturn(false);

        $authenticationService = $this->createPartialMock(Authentication::class, ['getUser']);
        $authenticationService->method('getUser')->willReturn($userDataType);

        $tokenInfrastructure = $this->createPartialMock(TokenInfrastructure::class, ['tokenDelete']);
        $tokenInfrastructure->method('tokenDelete')->with($userDataType)->willReturn(5);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authorization: $authorizationService,
            authentication: $authenticationService,
            tokenInfrastructure: $tokenInfrastructure,
        );

        $this->assertEquals(5, $sut->customerTokensDelete(new ID('_testuserid')));
    }

    public function testCustomerTokensDeleteOtherUserFails(): void
    {
        $authorizationService = $this->createPartialMock(Authorization::class, ['isAllowed']);
        $authorizationService->method('isAllowed')->willReturn(false);

        $userDataType = new UserDataType($this->getUserModelStub('_testuserid'));
        $authenticationService = $this->createPartialMock(Authentication::class, ['getUser']);
        $authenticationService->method('getUser')->willReturn($userDataType);

        $sut = $this->getSut(
            authorization: $authorizationService,
            authentication: $authenticationService,
        );

        $this->expectException(InvalidLogin::class);
        $sut->customerTokensDelete(new ID('_otheruserid'));
    }

    public function testCustomerTokensDeleteOtherUserAdmin(): void
    {
        $userDataType = new UserDataType($this->getUserModelStub('_testuserid'));

        $repositoryMock = $this->createPartialMock(BaseRepository::class, ['getById']);
        $repositoryMock->method('getById')->willReturn($userDataType);

        $authorizationService = $this->createPartialMock(Authorization::class, ['isAllowed']);
        $authorizationService->method('isAllowed')->willReturn(true);

        $authenticationService = $this->createPartialMock(Authentication::class, ['getUser']);
        $authenticationService->method('getUser')->willReturn($userDataType);

        $tokenInfrastructure = $this->createPartialMock(TokenInfrastructure::class, ['tokenDelete']);
        $tokenInfrastructure->method('tokenDelete')->with($userDataType)->willReturn(5);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authorization: $authorizationService,
            authentication: $authenticationService,
            tokenInfrastructure: $tokenInfrastructure,
        );

        $this->assertEquals(5, $sut->customerTokensDelete(new ID('_otheruserid')));
    }

    public function testShopTokensDelete(): void
    {
        $tokenInfrastructure = $this->createPartialMock(TokenInfrastructure::class, ['tokenDelete']);
        $tokenInfrastructure->method('tokenDelete')->with(null, null, 42)->willReturn(66);

        $legacyInfrastructure = $this->createPartialMock(LegacyInfrastructure::class, ['getShopId']);
        $legacyInfrastructure->method('getShopId')->willReturn(42);

        $sut = $this->getSut(
            tokenInfrastructure: $tokenInfrastructure,
            legacyInfrastructure: $legacyInfrastructure,
        );

        $this->assertEquals(66, $sut->shopTokensDelete());
    }

    public function testRegenerateSignatureKey(): void
    {
        $moduleSetupSpy = $this->createPartialMock(ModuleSetup::class, ['runSetup']);
        $moduleSetupSpy->expects($this->once())->method('runSetup');

        $sut = $this->getSut(moduleSetup: $moduleSetupSpy);

        $this->assertTrue($sut->regenerateSignatureKey());
    }

    private function getSut(
        ?BaseRepository $repository = null,
        ?Authorization $authorization = null,
        ?Authentication $authentication = null,
        ?TokenInfrastructure $tokenInfrastructure = null,
        ?LegacyInfrastructure $legacyInfrastructure = null,
        ?ModuleSetup $moduleSetup = null,
    ): TokenAdministration {
        return new TokenAdministration(
            $repository ?? $this->createStub(BaseRepository::class),
            $authorization ?? $this->createStub(Authorization::class),
            $authentication ?? $this->createStub(Authentication::class),
            $tokenInfrastructure ?? $this->createStub(TokenInfrastructure::class),
            $legacyInfrastructure ?? $this->createStub(LegacyInfrastructure::class),
            $moduleSetup ?? $this->createStub(ModuleSetup::class),
        );
    }
}
