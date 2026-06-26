<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\TokenPayload;
use OxidEsales\GraphQL\Base\DataType\TokenPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\TokensPayload;
use OxidEsales\GraphQL\Base\DataType\TokensPayloadInterface;
use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidRefreshToken;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Base\Service\RefreshTokenServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenAdministration;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Types\ID;

class Token
{
    public function __construct(
        private readonly TokenAdministration $tokenAdministration,
        private readonly Authentication $authentication,
        private readonly Authorization $authorization,
        private readonly TokenService $tokenService,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
    ) {
    }

    /**
     * Query of Base Module.
     * Query a customer's active JWT.
     * User with right 'VIEW_ANY_TOKEN' can query any customer's tokens.
     *
     * @Query(outputType="TokensPayload")
     * @Logged
     */
    public function tokens(
        ?TokenFilterList $filter = null,
        ?Pagination $pagination = null,
        ?TokenSorting $sort = null
    ): TokensPayloadInterface {
        try {
            return $this->tokenAdministration->tokens(
                $filter ?? new TokenFilterList(
                    new IDFilter($this->authentication->getUser()->id())
                ),
                $pagination ?? new Pagination(),
                $sort ?? new TokenSorting(Sorting::SORTING_ASC),
            );
        } catch (InvalidLogin) {
            return new TokensPayload([], [ValidationError::fromCode(ValidationError::INVALID_CREDENTIALS)]);
        }
    }

    /**
     * retrieve a new JWT for authentication by refresh token data
     *
     * @Query(outputType="TokenPayload")
     */
    public function refresh(string $refreshToken, string $fingerprintHash): TokenPayloadInterface
    {
        try {
            return $this->refreshTokenService->refreshToken($refreshToken, $fingerprintHash);
        } catch (FingerprintValidationException) {
            return new TokenPayload(null, [ValidationError::fromCode(ValidationError::INVALID_FINGERPRINT)]);
        } catch (InvalidRefreshToken) {
            return new TokenPayload(null, [ValidationError::fromCode(ValidationError::INVALID_REFRESH_TOKEN)]);
        } catch (TokenQuota) {
            return new TokenPayload(null, [AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED)]);
        }
    }

    /**
     * Mutation of Base Module.
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
     * Mutation of Base Module.
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
            return true;
        }

        $this->tokenService->deleteUserToken($this->authentication->getUser(), $tokenId);
        return true;
    }

    /**
     * Mutation of Base Module.
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
     * Mutation of Base Module.
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
