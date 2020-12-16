<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\DataType;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use Full\Qualified\Namespace\Context\DataType\Category as CategoryDataType;
use Full\Qualified\Namespace\Context\Exception\CategoryNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Category
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        $this->repository           = $repository;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @throws CategoryNotFound
     */
    public function category(string $id): CategoryDataType
    {
        try {
            /** @var CategoryDataType $category */
            $category = $this->repository->getById($id, CategoryDataType::class);
        } catch (NotFound $e) {
            throw CategoryNotFound::byId($id);
        }

        if ($category->isActive()) {
            return $category;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_CATEGORY')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $category;
    }
}
