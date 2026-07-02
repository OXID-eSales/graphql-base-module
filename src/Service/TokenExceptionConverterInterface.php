<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\Token as TokenAlias;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use TheCodingMachine\GraphQLite\Types\ID;

interface TokenExceptionConverterInterface
{
    /**
     * @return TokenAlias[]|ErrorInterface
     */
    public function tokens(
        TokenFilterList $filterList,
        Pagination $pagination,
        TokenSorting $sort
    ): array|ErrorInterface;

    public function refresh(string $refreshToken, string $fingerprintHash): UnencryptedToken|ErrorInterface;

    public function customerTokensDelete(?ID $customerId): int|ErrorInterface;

    public function deleteToken(ID $tokenId): true|ErrorInterface;

    public function deleteUserToken(UserInterface $user, ID $tokenId): true|ErrorInterface;
}
