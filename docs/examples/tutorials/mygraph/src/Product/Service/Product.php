<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Service;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use MyVendor\GraphQL\MyGraph\Product\Exception\ProductNotFound;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\ProductRepository;
use OxidEsales\GraphQL\Base\Exception\NotFound;

final class Product
{
    /** @var ProductRepository */
    private $productRepository;

    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @throws ProductNotFound
     */
    public function product(string $id): ProductDataType
    {
        try {
            $product = $this->productRepository->product($id);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($id);
        }

        return $product;
    }
}
