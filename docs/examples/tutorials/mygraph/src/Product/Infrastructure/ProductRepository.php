<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Infrastructure;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use OxidEsales\Eshop\Application\Model\Article as ProductEshopModel;
use OxidEsales\GraphQL\Base\Exception\NotFound;

final class ProductRepository
{
    /**
     * @throws NotFound
     */
    public function product(string $id): ProductDataType
    {
        /** @var ProductEshopModel */
        $product = oxNew(ProductEshopModel::class);

        if (!$product->load($id)) {
            throw new NotFound();
        }

        return new ProductDataType(
            $product
        );
    }
}
