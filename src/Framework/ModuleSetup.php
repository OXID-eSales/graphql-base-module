<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use Doctrine\DBAL\Exception\TableNotFoundException;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQl\Dao\TokenDao;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

/**
 * @internal
 */
class ModuleSetup
{
    /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
    private $queryBuilderFactory;

    /** @var  KeyRegistryInterface $keyRegistry */
    private $keyRegistry;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        KeyRegistryInterface $keyRegistry
    )
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->keyRegistry = $keyRegistry;
    }

    public function checkIfTokenTableExists()
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('COUNT(*)')->from(TokenDao::TOKENTABLE);
        try {
            $queryBuilder->execute();
        }
        catch (TableNotFoundException $e) {
            return false;
        }
        return true;
    }

    public function createTokenTable()
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $query = "CREATE TABLE " . TokenDao::TOKENTABLE;
        $query .= <<< EOQ
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255),
  token VARCHAR(2048),
  shopid int
)
EOQ;

        $queryBuilder->getConnection()->executeQuery($query);
    }

    public function createSignatureKey()
    {
        $config = Registry::getConfig();
        // Never overwrite the signature key because it will be
        // impossible to decode the existing tokens
        if ($config->getConfigParam === null) {
            $key = base64_encode(openssl_random_pseudo_bytes(64));
            $config = Registry::getConfig();
            $config->setConfigParam('strAuthTokenSignatureKey', $key);
            $config->saveShopConfVar('str', 'strAuthTokenSignatureKey', $key);
        }
    }

    public function runSetup()
    {
        if (! $this->checkIfTokenTableExists()) {
            $this->createTokenTable();
        }
        $this->createSignatureKey();
    }

    /**
     * Activation function for the module
     */
    public static function onActivate()
    {
        /** @var ModuleSetup $moduleSetup */
        $moduleSetup = ContainerFactory::getInstance()->getContainer()->get(ModuleSetup::class);
        $moduleSetup->runSetup();
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactive()
    {
    }
}
