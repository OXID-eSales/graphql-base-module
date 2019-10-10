<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Factory;

/**
 * @Type()
 */
class Address
{
    /** @var  string */
    private $street = '';

    /** @var  string */
    private $streetnr = '';

    /** @var  string */
    private $additionalinfo = '';

    /** @var  string */
    private $city = '';

    /** @var  string */
    private $zip = '';

    /** @var  string */
    private $countryshortcut = '';

    public function __construct(
        string $street = null,
        string $streetnr = null,
        string $additionalinfo = null,
        string $city = null,
        string $zip = null,
        string $countryshortcut = null
    ) {
        $this->street = $street;
        $this->streetnr = $streetnr;
        $this->additionalinfo = $additionalinfo;
        $this->city = $city;
        $this->zip = $zip;
        $this->countryshortcut = $countryshortcut;
    }

    /**
     * @Factory()
     */
    public static function createAddress(
        string $street = null,
        string $streetnr = null,
        string $additionalinfo = null,
        string $city = null,
        string $zip = null,
        string $countryshortcut = null
    ): self {
        return new self(
            $street,
            $streetnr,
            $additionalinfo,
            $city,
            $zip,
            $countryshortcut
        );
    }

    /**
     * @Field()
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @Field()
     */
    public function getStreetnr(): string
    {
        return $this->streetnr;
    }

    /**
     * @Field()
     */
    public function getAdditionalinfo(): string
    {
        return $this->additionalinfo;
    }

    /**
     * @Field()
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @Field()
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @Field()
     */
    public function getCountryshortcut(): string
    {
        return $this->countryshortcut;
    }
}
