<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Manufacturer\Infrastructure;

use OxidEsales\Eshop\Application\Model\Manufacturer as EshopManufacturerModel;
use MyVendor\GraphQL\MyGraph\Product\DataType\Manufacturer as ManufacturerDataType;
use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;

final class Product
{
    public function manufacturerByProduct(ProductDataType $product): ?ManufacturerDataType
    {
        /** @var null|EshopManufacturerModel $manufacturer */
        $manufacturer = $product->getEshopModel()->getManufacturer();

        if ($manufacturer === null) {
            return null;
        }

        return new ManufacturerDataType(
            $manufacturer
        );
    }
}
