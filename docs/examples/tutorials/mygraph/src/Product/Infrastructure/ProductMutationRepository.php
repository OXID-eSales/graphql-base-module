<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Infrastructure;

use MyVendor\GraphQL\MyGraph\Product\DataType\Product as ProductDataType;
use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Base\Exception\NotFound;

final class ProductMutationRepository
{
    /**
     * @throws NotFound
     */
    public function product(string $id): ProductDataType
    {
        /** @var EshopProductModel */
        $product = oxNew(EshopProductModel::class);

        if (!$product->load($id)) {
            throw new NotFound();
        }

        return new ProductDataType(
            $product
        );
    }

    /**
     * @return true
     */
    public function saveProduct(ProductDataType $product): bool
    {
        if (!$product->getEshopModel()->save()) {
            throw new RuntimeException('Object save failed');
        }

        return true;
    }
}
