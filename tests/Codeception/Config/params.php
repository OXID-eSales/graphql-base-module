<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Facts\Facts;
use OxidEsales\TestingLibrary\Services\Library\DatabaseDefaultsFileGenerator;

$facts = new Facts();
$php   = (getenv('PHPBIN')) ?: 'php';

return [
    'SHOP_URL'             => $facts->getShopUrl(),
    'SHOP_SOURCE_PATH'     => $facts->getSourcePath(),
    'VENDOR_PATH'          => $facts->getVendorPath(),
    'DB_NAME'              => $facts->getDatabaseName(),
    'DB_USERNAME'          => $facts->getDatabaseUserName(),
    'DB_PASSWORD'          => $facts->getDatabasePassword(),
    'DB_HOST'              => $facts->getDatabaseHost(),
    'DB_PORT'              => $facts->getDatabasePort(),
    'DUMP_PATH'            => getTestDataDumpFilePath(),
    'MYSQL_CONFIG_PATH'    => getMysqlConfigPath(),
    'PHP_BIN'              => $php,
];

function getTestDataDumpFilePath()
{
    return  __DIR__ . '/../../Fixtures/testdemodata.sql';
}

function getMysqlConfigPath()
{
    $facts      = new Facts();
    $configFile = new ConfigFile($facts->getSourcePath() . '/config.inc.php');

    $generator = new DatabaseDefaultsFileGenerator($configFile);

    return $generator->generate();
}
