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
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Infrastructure\Repository as BaseRepository;

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

    public function __construct(
        BaseRepository $repository,
        Authorization $authorizationService,
        Authentication $authenticationService
    ) {
        $this->repository            = $repository;
        $this->authorizationService  = $authorizationService;
        $this->authenticationService = $authenticationService;
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
}
