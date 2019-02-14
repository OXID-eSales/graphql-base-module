<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

class TokenRequest
{
    /** @var  string $username */
    private $username = null;
    /** @var  string $password */
    private $password = null;
    /** @var  string $group */
    private $group = null;
    /** @var  string lang */
    private $lang = null;
    /** @var  int $shopid */
    private $shopid = null;
    /** @var  Token $currentToken */
    private $currentToken = null;

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
    public function getPassword(): string
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
     * @return string
     */
    public function getGroup(): string
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
     * @return string
     */
    public function getLang(): string
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
     * @return int
     */
    public function getShopid(): int
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
     * @return Token
     */
    public function getCurrentToken(): Token
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
