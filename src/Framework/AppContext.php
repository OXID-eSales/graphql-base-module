<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace  OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\DataObject\Token;

class AppContext
{

    /** @var  Token $token */
    private $token = null;

    /** @var  int $defaultShopId */
    private $defaultShopId;

    /** @var  string $defaultShopLanguage */
    private $defaultShopLanguage;

    /** @var  string $shopUrl */
    private $shopUrl;

    public function getAuthToken(): Token
    {
        return $this->token;
    }

    public function setAuthToken(Token $token)
    {
        $this->token = $token;
    }

    public function hasAuthToken(): bool
    {
        return $this->token !== null;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getDefaultShopId(): int
    {
        return $this->defaultShopId;
    }

    /**
     * @param int $defaultShopId
     */
    public function setDefaultShopId(int $defaultShopId)
    {
        $this->defaultShopId = $defaultShopId;
    }

    /**
     * @return string
     */
    public function getDefaultShopLanguage(): string
    {
        return $this->defaultShopLanguage;
    }

    /**
     * @param string $defaultShopLanguage
     */
    public function setDefaultShopLanguage(string $defaultShopLanguage)
    {
        $this->defaultShopLanguage = $defaultShopLanguage;
    }

    /**
     * @return string
     */
    public function getShopUrl(): string
    {
        return $this->shopUrl;
    }

    /**
     * @param string $shopUrl
     */
    public function setShopUrl(string $shopUrl)
    {
        $this->shopUrl = $shopUrl;
    }

}
