<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Service;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\ProductRepository as ProductRepository;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\ProductMutation as ProductMutationService;
use MyVendor\GraphQL\MyGraph\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ProductTitleInput
{
    /** @var ProductRepository */
    private $productRepository;

    /** @var ProductMutationService */
    private $productMutationService;

    public function __construct(
        ProductRepository $productRepository,
        ProductMutationService $productMutationService
    ) {
        $this->productRepository = $productRepository;
        $this->productMutationService = $productMutationService;
    }

    /**
     * @Factory(name="ProductInput")
     */
    public function fromUserInput(string $productId, string $title): ProductDataType
    {
        try {
            $product = $this->productRepository->product($productId);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($productId);
        }

        return $this->productMutationService->assignTitle($product, $title);
    }
}
