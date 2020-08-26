<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Infrastructure;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;

final class ProductMutation
{
    public function assignTitle(
        ProductDataType $product,
        string $title
    ): ProductDataType {
        $product->getEshopModel()->assign([
            'oxtitle' => $title
        ]);

        return $product;
    }
}
