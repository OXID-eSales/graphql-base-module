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
use OxidEsales\GraphQL\Base\DataType\AccessToken as TokenDataType;
use OxidEsales\GraphQL\Base\DataType\AccessTokenFilterList;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\AccessToken as TokenService;
use OxidEsales\GraphQL\Base\Service\AccessTokenAdministration;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Types\ID;

class AccessToken
{
    /** @var AccessTokenAdministration */
    private $tokenAdministration;

    /** @var Authentication */
    private $authentication;

    /** @var Authorization */
    private $authorization;

    /** @var TokenService */
    private $tokenService;

    public function __construct(
        AccessTokenAdministration $tokenAdministration,
        Authentication $authentication,
        Authorization $authorization,
        TokenService $tokenService
    ) {
        $this->tokenAdministration = $tokenAdministration;
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->tokenService = $tokenService;
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
        ?AccessTokenFilterList $filter = null,
        ?Pagination $pagination = null,
        ?TokenSorting $sort = null
    ): array {
        return $this->tokenAdministration->tokens(
            $filter ?? AccessTokenFilterList::fromUserInput(
                new IDFilter(
                    $this->authentication->getUser()->id()
                )
            ),
            $pagination ?? new Pagination(),
            $sort ?? TokenSorting::fromUserInput(TokenSorting::SORTING_ASC)
        );
    }

    /**
     * retrieve a JWT for authentication of further requests
     *
     * @Query
     */
    public function accessToken(string $refreshToken): string
    {
        return $this->tokenService->createToken($refreshToken)->toString();
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
     * Invalidate specific token.
     *  - Customer with right INVALIDATE_ANY_TOKEN can invalidate any token.
     *  - Customer without special rights can invalidate only own token.
     *
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
     * INVALIDATE_ANY_TOKEN right is required.
     *
     * @Mutation
     * @Logged
     * @Right("INVALIDATE_ANY_TOKEN")
     */
    public function shopTokensDelete(): int
    {
        return $this->tokenAdministration->shopTokensDelete();
    }

    /**
     * Regenerates the JWT signature key.
     * This will invalidate all issued tokens for the current shop.
     * Only use if no other option is left.
     * REGENERATE_SIGNATURE_KEY right is required.
     *
     * @Mutation
     * @Logged
     * @Right("REGENERATE_SIGNATURE_KEY")
     */
    public function regenerateSignatureKey(): bool
    {
        return $this->tokenAdministration->regenerateSignatureKey();
    }
}
