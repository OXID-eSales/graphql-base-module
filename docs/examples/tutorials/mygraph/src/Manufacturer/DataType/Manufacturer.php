<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Manufacturer\DataType;

use OxidEsales\Eshop\Application\Model\Manufacturer as EshopManufacturerModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Manufacturer
{
    /** @var EshopManufacturerModel */
    private $manufacturer;

    public function __construct(
        EshopManufacturerModel $manufacturer
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
