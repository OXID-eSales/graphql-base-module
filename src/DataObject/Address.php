<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

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

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setFromDataArray($data);
        }
    }

    /**
     * @Field()
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    /**
     * @Field()
     */
    public function getStreetnr(): string
    {
        return $this->streetnr;
    }

    public function setStreetnr(string $streetnr)
    {
        $this->streetnr = $streetnr;
    }

    /**
     * @Field()
     */
    public function getAdditionalinfo(): string
    {
        return $this->additionalinfo;
    }

    public function setAdditionalinfo(string $additionalinfo)
    {
        $this->additionalinfo = $additionalinfo;
    }

    /**
     * @Field()
     */
    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @Field()
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip)
    {
        $this->zip = $zip;
    }

    /**
     * @Field()
     */
    public function getCountryshortcut(): string
    {
        return $this->countryshortcut;
    }

    public function setCountryshortcut(string $countryshortcut): void
    {
        $this->countryshortcut = $countryshortcut;
    }

    /**
     * @param array<string, mixed>
     */
    private function setFromDataArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getField(string $fieldname)
    {
        return $this->$fieldname;
    }

    public function setField(string $fieldname, $value)
    {
        return $this->$fieldname = $value;
    }
}
