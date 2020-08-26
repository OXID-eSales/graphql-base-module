<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\Controller;

use Full\Qualified\Namespace\Context\DataType\Category as CategoryDataType;
use Full\Qualified\Namespace\Context\Service\Category as CategoryService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Category
{
    /** @var CategoryService */
    private $categoryService;

    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    /**
     * @Query()
     */
    public function category(string $id): CategoryDataType
    {
        return $this->categoryService->category($id);
    }
}
