<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Controller;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use MyVendor\GraphQL\MyGraph\Product\Service\ProductMutation as ProductMutationService;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Logged;

final class ProductMutation
{
    /** @var ProductMutationService */
    private $productMutationService;

    public function __construct(
        ProductMutationService $ProductMutationService
    ) {
        $this->productMutationService = $ProductMutationService;
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function productTitleUpdate(ProductDataType $product): ProductDataType
    {
        $this->productMutationService->store($product);

        return $product;
    }
}
