<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\DataType;

use OxidEsales\Eshop\Application\Model\Manufacturer as ManufacturerEshopModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Manufacturer
{
    /** @var ManufacturerEshopModel */
    private $manufacturer;

    public function __construct(
        ManufacturerEshopModel $manufacturer
    ) {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->manufacturer->getId());
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->manufacturer->getTitle();
    }
}
