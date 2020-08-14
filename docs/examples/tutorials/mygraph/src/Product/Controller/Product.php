<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Controller;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use MyVendor\GraphQL\MyGraph\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Product
{
    /** @var ProductService */
    private $productService;

    public function __construct(
        ProductService $ProductService
    ) {
        $this->productService = $ProductService;
    }

    /**
     * @Query()
     */
    public function Product(string $id): ProductDataType
    {
        return $this->productService->product($id);
    }
}
