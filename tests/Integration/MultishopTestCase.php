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
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use OxidEsales\TestingLibrary\Services\ModuleInstaller\ModuleInstaller;

abstract class MultishopTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->getConfig()->getEdition() !== 'EE') {
            $this->markTestSkipped("Skip EE related tests for CE/PE edition");
        }

        $this->ensureShop(2);
        $this->cleanupCachedRegistry();
    }

    protected function tearDown(): void
    {
        $this->cleanupCachedRegistry();

        parent::tearDown();
    }

    protected function ensureShop(int $shopId = 2)
    {
        $database = DatabaseProvider::getDb();

        $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($shop->load($shopId)) {
            return;
        }

        $shop->assign([
            'OXID' => $shopId,
            'OXACTIVE' => 1,
            'OXNAME' => 'Second shop'
        ]);
        $shop->save();

        $copyVars = array(
            "aLanguages"
        );

        $select = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule)
            select MD5(RAND()), {$shopId} as oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule from oxconfig
                where oxshopid = '1' and oxvarname in ( '" . join("', '", $copyVars) . "') ";
        $database->execute($select);

        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoInterface::class)->get(1);
        $container->get(ShopConfigurationDaoInterface::class)->save(
            $shopConfiguration,
            $shopId
        );

        $metaData = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $metaData->updateViews();

        $moduleInstaller = new ModuleInstaller(Registry::getConfig());
        $moduleInstaller->switchToShop($shopId);

        $testConfig = $this->getTestConfig();
        $aInstallModules = $testConfig->getModulesToActivate();
        foreach ($aInstallModules as $modulePath) {
            $moduleInstaller->installModule($modulePath);
        }
    }

    protected function cleanupCachedRegistry()
    {
        $keepThese = [\OxidEsales\Eshop\Core\ConfigFile::class];
        $registryKeys = Registry::getKeys();
        foreach ($registryKeys as $key) {
            if (in_array($key, $keepThese)) {
                continue;
            }
            Registry::set($key, null);
        }
        $utilsObject = new \OxidEsales\Eshop\Core\UtilsObject();
        $utilsObject->resetInstanceCache();
        Registry::set(\OxidEsales\Eshop\Core\UtilsObject::class, $utilsObject);
    }
}
