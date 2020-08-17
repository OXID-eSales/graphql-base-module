<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Infrastructure;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use MyVendor\GraphQL\MyGraph\Product\DataType\Manufacturer as ManufacturerDataType;
use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;

final class ProductMutation
{
    public function assignTitle(ProductDataType $product, string $title): ProductDataType
    {
        /** @var EshopproductModel $product */
        $product->getEshopModel()->assign(
            [
                'oxtitle' => $title
            ]
        );

        return $product;
    }
}
