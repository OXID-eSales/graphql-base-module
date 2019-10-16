<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use OxidEsales\Eshop\Core\Registry;

class EnvironmentService implements EnvironmentServiceInterface
{
    public function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    public function getLanguage(): string
    {
        $language = Registry::getLang();
        return $language->getLanguageAbbr($language->getBaseLanguage());
    }

    public function getShopId(): int
    {
        return Registry::getConfig()->getShopId();
    }
}
