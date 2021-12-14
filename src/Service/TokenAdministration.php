<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Base\DataType\Sorting\TokenSorting;
use OxidEsales\GraphQL\Base\DataType\Token as TokenDataType;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Exception\UserNotFound;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyInfrastructure;
use OxidEsales\GraphQL\Base\Infrastructure\Repository as BaseRepository;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * Token data access service
 */
class TokenAdministration
{
    /** @var BaseRepository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    /** @var Authentication */
    private $authenticationService;

    /** @var TokenInfrastructure */
    private $tokenInfrastructure;

    /** @var LegacyInfrastructure */
    private $legacyInfrastructure;

    public function __construct(
        BaseRepository $repository,
        Authorization $authorizationService,
        Authentication $authenticationService,
        TokenInfrastructure $tokenInfrastructure,
        LegacyInfrastructure $legacyInfrastructure
    ) {
        $this->repository            = $repository;
        $this->authorizationService  = $authorizationService;
        $this->authenticationService = $authenticationService;
        $this->tokenInfrastructure   = $tokenInfrastructure;
        $this->legacyInfrastructure  = $legacyInfrastructure;
    }

    /**
     * @return TokenDataType[]
     */
    public function tokens(
        TokenFilterList $filterList,
        Pagination $pagination,
        TokenSorting $sort
    ): array {

        //without right to view any token user can only add filter on own id or no filter on id
        if (!$this->authorizationService->isAllowed('VIEW_ANY_TOKEN') &&
            ($userFilter = $filterList->getUserFilter()) &&
            $this->authenticationService->getUser()->id()->val() !== $userFilter->equals()->val()
        ) {
            throw new InvalidLogin('Unauthorized');
        }

        return $this->repository->getList(
            TokenDataType::class,
            $filterList,
            $pagination,
            $sort
        );
    }

    /**
     * @throws \OxidEsales\GraphQL\Base\Exception\NotFound
     */
    public function customerTokensDelete(?ID $customerId): int
    {
        if (!$this->authorizationService->isAllowed('INVALIDATE_ANY_TOKEN') &&
            $customerId &&
            $this->authenticationService->getUser()->id()->val() !== $customerId->val()
        ) {
            throw new InvalidLogin('Unauthorized');
        }

        $id = $customerId ? (string) $customerId->val() : (string) $this->authenticationService->getUser()->id()->val();

        try {
            /** @var UserDataType $user */
            $user = $this->repository->getById(
                $id,
                UserDataType::class
            );
        } catch (NotFound $e) {
            throw UserNotFound::byId($id);
        }

        return $this->tokenInfrastructure->tokenDelete($user);
    }

    public function shopTokensDelete(): int
    {
        return $this->tokenInfrastructure->tokenDelete(null, null, $this->legacyInfrastructure->getShopId());
    }
}
