<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\ExceptionConverter;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\AuthorizationError;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Error\NotFoundError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidRefreshToken;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Exception\UnknownToken;
use OxidEsales\GraphQL\Base\Exception\UserNotFound;
use OxidEsales\GraphQL\Base\Service\RefreshTokenServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token;
use OxidEsales\GraphQL\Base\Service\TokenAdministration;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class TokenExceptionConverter implements TokenExceptionConverterInterface
{
    public function __construct(
        private readonly TokenAdministration $tokenAdministration,
        private readonly Token $tokenService,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function tokens(
        TokenFilterList $filterList,
        Pagination $pagination,
        TokenSorting $sort
    ): array|ErrorInterface {
        try {
            return $this->tokenAdministration->tokens($filterList, $pagination, $sort);
        } catch (InvalidLogin) {
            return AuthorizationError::fromCode(AuthorizationError::UNAUTHORIZED_VIEW_TOKEN);
        }
    }

    public function refresh(string $refreshToken, string $fingerprintHash): UnencryptedToken|ErrorInterface
    {
        try {
            return $this->refreshTokenService->refreshToken($refreshToken, $fingerprintHash);
        } catch (FingerprintValidationException) {
            return ValidationError::fromCode(ValidationError::FINGERPRINT, $fingerprintHash);
        } catch (InvalidRefreshToken) {
            return ValidationError::fromCode(ValidationError::REFRESH_TOKEN, $refreshToken);
        } catch (TokenQuota) {
            return AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED);
        }
    }

    public function customerTokensDelete(?ID $customerId): int|ErrorInterface
    {
        try {
            return $this->tokenAdministration->customerTokensDelete($customerId);
        } catch (InvalidLogin) {
            return AuthorizationError::fromCode(AuthorizationError::UNAUTHORIZED_DELETE_TOKEN);
        } catch (UserNotFound) {
            return NotFoundError::fromCode(NotFoundError::USER, (string)$customerId);
        }
    }

    public function deleteToken(ID $tokenId): true|ErrorInterface
    {
        try {
            $this->tokenService->deleteToken($tokenId);
            return true;
        } catch (UnknownToken) {
            return NotFoundError::fromCode(NotFoundError::TOKEN, (string)$tokenId);
        }
    }

    public function deleteUserToken(UserInterface $user, ID $tokenId): true|ErrorInterface
    {
        try {
            $this->tokenService->deleteUserToken($user, $tokenId);
            return true;
        } catch (UnknownToken) {
            return NotFoundError::fromCode(NotFoundError::TOKEN, (string)$tokenId);
        }
    }

    public function shopTokensDelete(): int
    {
        return $this->tokenAdministration->shopTokensDelete();
    }

    public function regenerateSignatureKey(): bool
    {
        return $this->tokenAdministration->regenerateSignatureKey();
    }
}
