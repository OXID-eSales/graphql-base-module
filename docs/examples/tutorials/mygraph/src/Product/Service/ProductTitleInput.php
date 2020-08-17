<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Service;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\ProductMutationRepository as ProductRepository;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\ProductMutation as ProductMutationInfrastructure;
use MyVendor\GraphQL\MyGraph\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ProductTitleInput
{
    /** @var ProductRepository */
    private $productRepository;

    /** @var ProductMutationInfrastructure */
    private $productMutationInfrastructure;

    public function __construct(
        ProductRepository $productRepository,
        ProductMutationInfrastructure $productMutationInfrastructure
    ) {
        $this->productRepository = $productRepository;
        $this->productMutationInfrastructure = $productMutationInfrastructure;
    }

    /**
     * @Factory(name="ProductTitleInput")
     */
    public function fromUserInput(string $productId, string $title): ProductDataType
    {
        try {
            $product = $this->productRepository->product($productId);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($productId);
        }

        return $this->productMutationInfrastructure->assignTitle($product, $title);
    }
}
