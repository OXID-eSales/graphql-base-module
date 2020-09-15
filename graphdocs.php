<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

define('AUTOLOAD_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
define('VENDOR_OX_BASE_PATH', AUTOLOAD_PATH . 'oxid-esales' . DIRECTORY_SEPARATOR . 'oxideshop-ce' . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
require_once VENDOR_OX_BASE_PATH . DIRECTORY_SEPARATOR . "bootstrap.php";

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;
use OxidEsales\EshopCommunity\Setup\Database as SetupDatabase;

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

function prepareDatabase()
{
    $files = [
        'schema' => VENDOR_OX_BASE_PATH . 'Setup' . DIRECTORY_SEPARATOR . 'Sql' . DIRECTORY_SEPARATOR . 'database_schema.sql',
        'initialData' => VENDOR_OX_BASE_PATH . 'Setup' . DIRECTORY_SEPARATOR . 'Sql' . DIRECTORY_SEPARATOR . 'initial_data.sql'
    ];

    $connection = getDatabaseConnection ();

    foreach ($files as $file) {
        foreach (getQueries($file) as $query) {
            $connection->exec($query);
        }
    }
}

prepareDatabase();

ContainerFactory::getInstance()
    ->getContainer()
    ->get(GraphQLQueryHandler::class)
    ->executeGraphQLQuery();
