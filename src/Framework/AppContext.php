<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace  OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

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

    public static function createAppContext(
        EnvironmentServiceInterface $environmentService,
        KeyRegistryInterface $keyRegistry
    ): self {
        $appContext = new self();
        $appContext->setShopUrl($environmentService->getShopUrl());
        $appContext->setDefaultShopId($environmentService->getDefaultShopId());
        $appContext->setDefaultShopLanguage($environmentService->getDefaultLanguage());
        return $appContext;
    }

    public function getUserGroup(): string
    {
        if ($this->token) {
            return $this->token->getUserGroup();
        } else {
            return 'unknown';
        }
    }

    /**
     * @return Token|null
     */
    public function getAuthToken()
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

    public function getCurrentLanguage()
    {
        if ($this->hasAuthToken()) {
            return $this->token->getLang();
        } else {
            return $this->getDefaultShopLanguage();
        }
    }

    public function getCurrentShopId()
    {
        if ($this->hasAuthToken()) {
            return $this->token->getShopid();
        } else {
            return $this->getDefaultShopId();
        }
    }
}
