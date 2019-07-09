<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class LoginAcceptanceTest
 *
 * @package OxidEsales\GraphQl\Tests\Acceptance
 */
class ConfigCheckAcceptanceTest extends BaseGraphQlAcceptanceTestCase
{
    private $originalContainer = null;

    public function setUp()
    {
        parent::setUp();

        $this->createSecondShop();

        $oxConfig = Registry::getConfig();
        $oxConfig->saveShopConfVar('string', 'sTestParam', 'testValue1', '1');
        $oxConfig->saveShopConfVar('string', 'sTestParam', 'testValue2', '2');

    }

    public function tearDown()
    {
        $this->restoreOriginalContainer();
        parent::tearDown();
    }

    protected function beforeContainerCompile()
    {
        $loader = new YamlFileLoader($this->container, new FileLocator());
        $serviceFile = __DIR__ . DIRECTORY_SEPARATOR . 'Provider' . DIRECTORY_SEPARATOR . 'services.yaml';
        $loader->load($serviceFile);
    }

    /**
     * Test that shop2 will have the altered config value
     *
     */
    public function testConfigShop()
    {
        $query = "query ConfigCheck {configtest}";

        $token = $this->createToken('anonymous');
        $token->setShopid(2);

        $this->resetConfig();
        $this->setTestContainer();

        $this->executeQueryWithToken($query, $token);

        $this->assertHttpStatusOK();
        $this->assertEquals('testValue2', $this->queryResult['data']['configtest']);
    }

    private function resetConfig()
    {

        $reflectionClass = new \ReflectionClass(Registry::class);
        $instancesProperty = $reflectionClass->getProperty('instances');
        $instancesProperty->setAccessible(true);
        $instances = $instancesProperty->getValue();
        unset($instances[Config::class]);
        $instancesProperty->setValue($instances);

    }

    private function setTestContainer()
    {

        $reflectionClass = new \ReflectionClass((ContainerFactory::class));
        $containerProperty = $reflectionClass->getProperty('symfonyContainer');
        $containerProperty->setAccessible(true);
        if ($this->originalContainer === null) {
            $this->originalContainer = $containerProperty->getValue(ContainerFactory::getInstance());
        }
        $containerProperty->setValue(ContainerFactory::getInstance(), $this->container);
    }

    private function restoreOriginalContainer()
    {
        if ($this->originalContainer === null) {
            return;
        }
        $reflectionClass = new \ReflectionClass((ContainerFactory::class));
        $containerProperty = $reflectionClass->getProperty('symfonyContainer');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue(ContainerFactory::getInstance(), $this->originalContainer);

    }

}
