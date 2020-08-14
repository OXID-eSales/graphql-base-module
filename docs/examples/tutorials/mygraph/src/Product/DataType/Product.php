<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article as ProductEshopModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Product
{
    /** @var ProductEshopModel */
    private $product;

    public function __construct(
        ProductEshopModel $product
    ) {
        $this->product = $product;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID(
            $this->product->getId()
        );
    }

    /**
     * @Field()
     */
    public function title(): string
    {
        return (string) $this->product->getFieldData('oxtitle');
    }
}
