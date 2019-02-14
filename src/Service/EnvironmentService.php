<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\Eshop\Core\Registry;

class EnvironmentService implements EnvironmentServiceInterface
{
    public function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    public function getDefaultLanguage(): string
    {
        $language = Registry::getLang();
        return $language->getLanguageAbbr($language->getBaseLanguage());
    }

    public function getDefaultShopId(): int
    {
        return \OxidEsales\Eshop\Core\ShopIdCalculator::BASE_SHOP_ID;
    }

}
