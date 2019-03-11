<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

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
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreetnr(): string
    {
        return $this->streetnr;
    }

    /**
     * @param string $streetnr
     */
    public function setStreetnr(string $streetnr)
    {
        $this->streetnr = $streetnr;
    }

    /**
     * @return string
     */
    public function getAdditionalinfo(): string
    {
        return $this->additionalinfo;
    }

    /**
     * @param string $additionalinfo
     */
    public function setAdditionalinfo(string $additionalinfo)
    {
        $this->additionalinfo = $additionalinfo;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getCountryshortcut(): string
    {
        return $this->countryshortcut;
    }

    /**
     * @param string $countryshortcut
     */
    public function setCountryshortcut(string $countryshortcut)
    {
        $this->countryshortcut = $countryshortcut;
    }

    private function setFromDataArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getField(string $fieldname)
    {
        return $this->$fieldname;
    }

    public function setField(string $fieldname, $value)
    {
        return $this->$fieldname = $value;
    }
}
