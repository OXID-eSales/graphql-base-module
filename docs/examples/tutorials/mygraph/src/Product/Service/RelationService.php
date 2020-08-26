<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Service;

use MyVendor\GraphQL\MyGraph\Product\DataType\Manufacturer;
use MyVendor\GraphQL\MyGraph\Product\DataType\Product;
use MyVendor\GraphQL\MyGraph\Product\Infrastructure\Product as ProductInfrastructure;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Product::class)
 */
final class RelationService
{
    /** @var ProductInfrastructure */
    private $productInfrastructure;

    public function __construct(
        ProductInfrastructure $productInfrastructure
    ) {
        $this->productInfrastructure = $productInfrastructure;
    }

    /**
     * @Field()
     */
    public function manufacturer(Product $product): ?Manufacturer
    {
        return $this->productInfrastructure
                    ->manufacturerByProduct($product);
    }
}
