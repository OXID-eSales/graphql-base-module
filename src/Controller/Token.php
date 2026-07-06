<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Payload\CreationPayload;
use OxidEsales\GraphQL\Base\DataType\Payload\CreationPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenDeletePayload;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenDeletePayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenPayload;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokensPayload;
use OxidEsales\GraphQL\Base\DataType\Payload\TokensPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\TokenExceptionConverterInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Token
{
    public function __construct(
        private readonly Authentication $authentication,
        private readonly Authorization $authorization,
        private readonly TokenExceptionConverterInterface $tokenExceptionConverter,
    ) {
    }

    /**
     * Query of Base Module.
     * Query a customer's active JWT.
     * User with right 'VIEW_ANY_TOKEN' can query any customer's tokens.
     */
    #[Query(outputType: 'TokensPayload')]
    #[Logged]
    public function tokens(
        ?TokenFilterList $filter = null,
        ?Pagination $pagination = null,
        ?TokenSorting $sort = null
    ): TokensPayloadInterface {
        $result = $this->tokenExceptionConverter->tokens(
            $filter ?? new TokenFilterList(
                new IDFilter($this->authentication->getUser()->id())
            ),
            $pagination ?? new Pagination(),
            $sort ?? new TokenSorting(Sorting::SORTING_ASC),
        );

        if ($result instanceof ErrorInterface) {
            return new TokensPayload(null, [$result]);
        }

        return new TokensPayload($result);
    }

    /**
     * retrieve a new JWT for authentication by refresh token data
     */
    #[Query(outputType: 'TokenPayload')]
    public function refresh(string $refreshToken, string $fingerprintHash): TokenPayloadInterface
    {
        $result = $this->tokenExceptionConverter->refresh($refreshToken, $fingerprintHash);

        if ($result instanceof ErrorInterface) {
            return new TokenPayload(null, [$result]);
        }

        return new TokenPayload($result->toString());
    }

    /**
     * Mutation of Base Module.
     * Invalidate all tokens per customer.
     *  - Customer with right INVALIDATE_ANY_TOKEN can invalidate tokens for any customer Id.
     *  - Customer without special rights can invalidate only own tokens.
     * If no customerId is supplied, own Id is taken.
     */
    #[Mutation(outputType: 'TokenDeletePayload')]
    #[Logged]
    public function customerTokensDelete(?ID $customerId): TokenDeletePayloadInterface
    {
        $result = $this->tokenExceptionConverter->customerTokensDelete($customerId);

        if ($result instanceof ErrorInterface) {
            return new TokenDeletePayload(null, [$result]);
        }

        return new TokenDeletePayload($result);
    }

    /**
     * Mutation of Base Module.
     * Invalidate specific token.
     *  - Customer with right INVALIDATE_ANY_TOKEN can invalidate any token.
     *  - Customer without special rights can invalidate only own token.
     */
    #[Mutation(outputType: 'TokenDeletePayload')]
    #[Logged]
    public function tokenDelete(ID $tokenId): TokenDeletePayloadInterface
    {
        if ($this->authorization->isAllowed('INVALIDATE_ANY_TOKEN')) {
            $result = $this->tokenExceptionConverter->deleteToken($tokenId);
        } else {
            $result = $this->tokenExceptionConverter->deleteUserToken(
                $this->authentication->getUser(),
                $tokenId
            );
        }

        if ($result instanceof ErrorInterface) {
            return new TokenDeletePayload(null, [$result]);
        }

        return new TokenDeletePayload(1);
    }

    /**
     * Mutation of Base Module.
     * Invalidate all tokens for current shop.
     * INVALIDATE_ANY_TOKEN right is required.
     */
    #[Mutation(outputType: 'TokenDeletePayload')]
    #[Logged]
    #[Right('INVALIDATE_ANY_TOKEN')]
    public function shopTokensDelete(): TokenDeletePayloadInterface
    {
        return new TokenDeletePayload($this->tokenExceptionConverter->shopTokensDelete());
    }

    /**
     * Mutation of Base Module.
     * Regenerates the JWT signature key.
     * This will invalidate all issued tokens for the current shop.
     * Only use if no other option is left.
     * REGENERATE_SIGNATURE_KEY right is required.
     */
    #[Mutation(outputType: 'CreationPayload')]
    #[Logged]
    #[Right('REGENERATE_SIGNATURE_KEY')]
    public function regenerateSignatureKey(): CreationPayloadInterface
    {
        return new CreationPayload($this->tokenExceptionConverter->regenerateSignatureKey());
    }
}
