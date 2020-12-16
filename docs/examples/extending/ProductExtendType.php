<?php

declare(strict_types=1);

namespace Full\Quallified\Namespace\Service;

use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;

/**
 * @ExtendType(class=Product::class)
 */
final class ProductExtendType
{
    /**
     * @Field()
     */
    public function subtitle(Product $product): string
    {
        return $product->getEshopModel()->subtitle();
    }
}
