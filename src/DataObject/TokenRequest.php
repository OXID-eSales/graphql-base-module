<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

class TokenRequest
{
    /** @var  null|string $username */
    private $userid = null;
    /** @var  null|string $username */
    private $username = null;
    /** @var  null|string $password */
    private $password = null;
    /** @var  null|string $group */
    private $group = null;
    /** @var  null|string lang */
    private $lang = null;
    /** @var  null|int $shopid */
    private $shopid = null;
    /** @var  null|Token $currentToken */
    private $currentToken = null;

    /**
     * @return null|string
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param null|string $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return null|string
     */
    public function getUsername()
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
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return null|string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup(string $group)
    {
        $this->group = $group;
    }

    /**
     * @return null|string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return null|int
     */
    public function getShopid()
    {
        return $this->shopid;
    }

    /**
     * @param int $shopid
     */
    public function setShopid(int $shopid)
    {
        $this->shopid = $shopid;
    }

    /**
     * @return null|Token
     */
    public function getCurrentToken()
    {
        return $this->currentToken;
    }

    /**
     * @param Token $currentToken
     */
    public function setCurrentToken(Token $currentToken)
    {
        $this->currentToken = $currentToken;
    }
}
