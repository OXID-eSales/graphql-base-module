<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Service;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\ProductMutationRepository;
use OxidEsales\GraphQL\Base\Exception\NotFound;

final class ProductMutation
{
    /** @var ProductMutationRepository */
    private $productMutationRepository;

    public function __construct(
        ProductMutationRepository $productMutationRepository
    ) {
        $this->productMutationRepository = $productMutationRepository;
    }

    /**
     * @return true
     */
    public function store(ProductDataType $product): bool
    {
        return $this->productMutationRepository->saveProduct(
            $product
        );
    }
}
