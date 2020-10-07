<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

define('VENDOR_OX_BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
require_once VENDOR_OX_BASE_PATH . DIRECTORY_SEPARATOR . "bootstrap.php";

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting as ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Setup\Database as SetupDatabase;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;

/**
 * @param string $file
 *
 * @return array
 */
function getQueries($file)
{
    $fp = @fopen($file, "r");
    $contents = fread($fp, filesize($file));
    fclose($fp);
    $setupDatabase = new SetupDatabase();

    return $setupDatabase->parseQuery($contents);
}

/**
 * @return PDO
 */
function getDatabaseConnection()
{
    $bootstrap = new BootstrapConfigFileReader();
    $dbName = $bootstrap->dbName;
    $dsn = sprintf('mysql:host=%s;port=%s', $bootstrap->dbHost, $bootstrap->dbPort);
    $connection = new PDO(
        $dsn,
        $bootstrap->dbUser,
        $bootstrap->dbPwd,
        [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
    );
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    try {
        $connection->exec("DROP database `$dbName`");
    } catch (\Exception $exception) {
        //do nothing, db already dropped
    }
    $connection->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8 COLLATE utf8_general_ci;");
    $connection->exec("USE `$dbName`");
    $connection->exec("SET @@session.sql_mode = ''");

    return $connection;
}

function generateViews(): void
{
    $bootstrap = dirname(__FILE__) . DIRECTORY_SEPARATOR .
        'vendor' . DIRECTORY_SEPARATOR .
        'oxid-esales' . DIRECTORY_SEPARATOR .
        'oxideshop-ce' . DIRECTORY_SEPARATOR .
        'source' . DIRECTORY_SEPARATOR .
        'bootstrap.php';

    shell_exec("ESHOP_BOOTSTRAP_PATH=$bootstrap vendor/bin/oe-eshop-db_views_generate");
}

function activateGraphQLBaseModule(): void
{
    /** @var KeyRegistry $keyRegistry */
    $keyRegistry = ContainerFactory::getInstance()
        ->getContainer()
        ->get(KeyRegistry::class);

    $moduleSetting = new ModuleSetting();
    $moduleSetting
        ->setName(KeyRegistry::SIGNATUREKEYNAME)
        ->setType('str')
        ->setValue($keyRegistry->generateSignatureKey());

    $moduleConfiguration = new ModuleConfiguration();
    $moduleConfiguration->setId('oe/graphql-base');
    $moduleConfiguration->setPath(
        dirname(__FILE__) . DIRECTORY_SEPARATOR .
        'vendor' . DIRECTORY_SEPARATOR .
        'oxid-esales' . DIRECTORY_SEPARATOR .
        'graphql-base' . DIRECTORY_SEPARATOR
    );
    $moduleConfiguration->addModuleSetting($moduleSetting);

    /** @var ModuleConfigurationDaoBridgeInterface $moduleConfigurationDao */
    $moduleConfigurationDao = ContainerFactory::getInstance()
        ->getContainer()
        ->get(ModuleConfigurationDaoBridgeInterface::class);

    $moduleConfigurationDao->save($moduleConfiguration);

    /** @var ModuleActivationBridgeInterface $settingDao */
    $settingDao = ContainerFactory::getInstance()
        ->getContainer()
        ->get(ModuleActivationBridgeInterface::class);

    $settingDao->activate('oe/graphql-base', 1);
}

function prepareDatabase()
{
    $files = [
        'schema' => VENDOR_OX_BASE_PATH . 'Setup' . DIRECTORY_SEPARATOR . 'Sql' . DIRECTORY_SEPARATOR . 'database_schema.sql',
        'initialData' => VENDOR_OX_BASE_PATH . 'Setup' . DIRECTORY_SEPARATOR . 'Sql' . DIRECTORY_SEPARATOR . 'initial_data.sql'
    ];

    $connection = getDatabaseConnection();

    foreach ($files as $file) {
        foreach (getQueries($file) as $query) {
            $connection->exec($query);
        }
    }

    // In order to use the token query, we need to generate views and signature key.
    generateViews();
    activateGraphQLBaseModule();
}

prepareDatabase();
