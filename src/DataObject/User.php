<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

class User
{
    /** @var  string */
    private $id = '';
    /** @var  int|null */
    private $shopid = null;
    /** @var  string */
    private $username = '';
    /** @var  string */
    private $passwordhash = '';
    /** @var  string */
    private $passwordsalt = '';
    /** @var  string */
    private $firstname = '';
    /** @var  string */
    private $lastname = '';
    /** @var  string */
    private $usergroup = '';
    /** @var  Address */
    private $address;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getShopid()
    {
        return $this->shopid;
    }

    /**
     * @param int|null $shopid
     */
    public function setShopid($shopid)
    {
        $this->shopid = $shopid;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPasswordhash(): string
    {
        return $this->passwordhash;
    }

    /**
     * @param string $passwordhash
     */
    public function setPasswordhash(string $passwordhash)
    {
        $this->passwordhash = $passwordhash;
    }

    /**
     * @return string
     */
    public function getPasswordsalt(): string
    {
        return $this->passwordsalt;
    }

    /**
     * @param string $passwordsalt
     */
    public function setPasswordsalt(string $passwordsalt)
    {
        $this->passwordsalt = $passwordsalt;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getUsergroup(): string
    {
        return $this->usergroup;
    }

    /**
     * @param string $usergroup
     */
    public function setUsergroup(string $usergroup)
    {
        $this->usergroup = $usergroup;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        if (! $this->address) {
            return new Address();
        }
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

}
