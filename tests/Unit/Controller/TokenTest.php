<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use OxidEsales\GraphQL\Base\Controller\Token as TokenController;
use OxidEsales\GraphQL\Base\DataType\Filter\DateFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenAdministration as TokenAdministration;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use TheCodingMachine\GraphQLite\Types\ID;

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
                TokenFilterList::fromUserInput(new IDFilter($authentication->getUser()->id())),
                new Pagination(),
                TokenSorting::fromUserInput(TokenSorting::SORTING_ASC)
            )
            ->willReturn([]);

        $tokenController = $this->getTokenController($tokenAdministration, $authentication);
        $tokenController->tokens();
    }

    public function testTokensQueryWithCustomFilters(): void
    {
        $authentication = $this->createPartialMock(Authentication::class, ['getUser']);
        $authentication->method('getUser')
            ->willReturn(new UserDataType($this->getUserModelStub('_testuserid')));

        $filterList = TokenFilterList::fromUserInput(
            new IDFilter(new ID('someone_else')),
            new IDFilter(new ID(1)),
            new DateFilter(null, ['2021-01-12 12:12:12', '2021-12-31 12:12:12'])
        );
        $sort       = TokenSorting::fromUserInput(TokenSorting::SORTING_DESC);
        $pagination = Pagination::fromUserInput(10, 20);

        $tokenAdministration = $this->createPartialMock(TokenAdministration::class, ['tokens']);
        $tokenAdministration->method('tokens')
            ->with(
                $filterList,
                $pagination,
                $sort
            )
            ->willReturn([]);

        $tokenController = $this->getTokenController($tokenAdministration, $authentication);
        $tokenController->tokens($filterList, $pagination, $sort);
    }

    public function testCustomerTokensDelete(): void
    {
        $authentication      = $this->createPartialMock(Authentication::class, []);
        $tokenAdministration = $this->createPartialMock(TokenAdministration::class, ['customerTokensDelete']);
        $tokenAdministration->method('customerTokensDelete')
            ->willReturn(5);

        $tokenController = $this->getTokenController($tokenAdministration, $authentication);
        $tokenController->customerTokensDelete(new ID('someUserId'));
    }

    private function getTokenController(
        ?TokenAdministration $tokenAdministration = null,
        ?Authentication $authentication = null,
        ?Authorization $authorization = null,
        ?TokenService $tokenService = null
    ): TokenController {
        if (null === $tokenAdministration) {
            $tokenAdministration = $this->createMock(TokenAdministration::class);
        }

        if (null === $authentication) {
            $authentication = $this->createMock(Authentication::class);
        }

        if (null === $authorization) {
            $authorization = $this->createMock(Authorization::class);
        }

        if (null === $tokenService) {
            $tokenService = $this->createMock(TokenService::class);
        }

        return new TokenController($tokenAdministration, $authentication, $authorization, $tokenService);
    }
}
