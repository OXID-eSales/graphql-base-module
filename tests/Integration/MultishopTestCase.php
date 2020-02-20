<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\TestingLibrary\Services\ModuleInstaller\ModuleInstaller;

abstract class MultishopTestCase extends EnterpriseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureShop(2);
        $this->cleanupCachedRegistry();
    }

    protected function tearDown(): void
    {
        $this->cleanupCachedRegistry();

        parent::tearDown();
    }

    protected function ensureShop(int $shopId = 2): void
    {
        $database = DatabaseProvider::getDb();

        $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);

        if ($shop->load($shopId)) {
            return;
        }

        $shop->assign([
            'OXID'     => $shopId,
            'OXACTIVE' => 1,
            'OXNAME'   => 'Second shop',
        ]);
        $shop->save();

        $copyVars = [
            'aLanguages',
        ];

        // copy language settings from shop 1
        $database->execute("
            INSERT INTO oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule)
            SELECT MD5(RAND()), {$shopId} AS oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule FROM oxconfig
            WHERE oxshopid = '1'
              AND oxvarname IN ( '" . implode("', '", $copyVars) . "')
        ");

        $container         = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoInterface::class)->get(1);
        $container->get(ShopConfigurationDaoInterface::class)->save(
            $shopConfiguration,
            $shopId
        );

        $metaData = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $metaData->updateViews();

        $moduleInstaller = new ModuleInstaller(Registry::getConfig());
        $moduleInstaller->switchToShop($shopId);

        $testConfig      = $this->getTestConfig();
        $aInstallModules = $testConfig->getModulesToActivate();

        foreach ($aInstallModules as $modulePath) {
            $moduleInstaller->installModule($modulePath);
        }
    }

    protected function cleanupCachedRegistry(): void
    {
        Registry::getConfig()->setConfig(null);
        $utilsObject = new \OxidEsales\Eshop\Core\UtilsObject();
        $utilsObject->resetInstanceCache();

        $keepThese = [
            \OxidEsales\Eshop\Core\ConfigFile::class,
        ];
        $registryKeys = Registry::getKeys();

        foreach ($registryKeys as $key) {
            if (in_array($key, $keepThese)) {
                continue;
            }
            Registry::set($key, null);
        }
    }
}
