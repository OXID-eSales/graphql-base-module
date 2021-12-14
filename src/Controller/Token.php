<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\Token as TokenDataType;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenAdministration;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Types\ID;

class Token
{
    /** @var TokenAdministration */
    private $tokenAdministration;

    /** @var Authentication */
    private $authentication;

    /** @var Authorization */
    private $authorization;

    /** @var TokenService */
    private $tokenService;

    public function __construct(
        TokenAdministration $tokenAdministration,
        Authentication $authentication,
        Authorization $authorization,
        TokenService $tokenService
    ) {
        $this->tokenAdministration = $tokenAdministration;
        $this->authentication      = $authentication;
        $this->authorization       = $authorization;
        $this->tokenService        = $tokenService;
    }

    /**
     * Query a customer's active JWT.
     * User with right 'VIEW_ANY_TOKEN' can query any customer's tokens.
     *
     * @Query
     * @Logged
     *
     * @return TokenDataType[]
     */
    public function tokens(
        ?TokenFilterList $filter = null,
        ?Pagination      $pagination = null,
        ?TokenSorting    $sort = null
    ): array {
        return $this->tokenAdministration->tokens(
            $filter ?? TokenFilterList::fromUserInput(
                new IDFilter(
                    $this->authentication->getUser()->id()
                )
            ),
            $pagination ?? new Pagination(),
            $sort ?? TokenSorting::fromUserInput(TokenSorting::SORTING_ASC)
        );
    }

    /**
     * Invalidate all tokens per customer.
     *  - Customer with right INVALIDATE_ANY_TOKEN can invalidate tokens for any customer Id.
     *  - Customer without special rights can invalidate only own tokens.
     * If no customerId is supplied, own Id is taken.
     *
     * @Mutation
     * @Logged
     */
    public function customerTokensDelete(?ID $customerId): int
    {
        return $this->tokenAdministration->customerTokensDelete($customerId);
    }

    /**
     * @Mutation
     * @Logged
     */
    public function tokenDelete(ID $tokenId): bool
    {
        if ($this->authorization->isAllowed('INVALIDATE_ANY_TOKEN')) {
            $this->tokenService->deleteToken($tokenId);
        } else {
            $this->tokenService->deleteUserToken($this->authentication->getUser(), $tokenId);
        }

        return true;
    }
    /**
     * Invalidate all tokens for current shop.
     *
     * @Mutation
     * @Logged
     * @Right("INVALIDATE_ANY_TOKEN")
     */
    public function shopTokensDelete(): int
    {
        return $this->tokenAdministration->shopTokensDelete();
    }
}
