<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Config;

use OxidEsales\Codeception\Module\Database;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Path;

if ($shopRootPath = getenv('SHOP_ROOT_PATH')) {
    require_once(Path::join($shopRootPath, 'source', 'bootstrap.php'));
}

class CodeceptionParametersProvider {

    public function getParameters(): array
    {
        $facts = new Facts();
        $php = (getenv('PHPBIN')) ?: 'php';

        return [
            'SHOP_URL' => $facts->getShopUrl(),
            'SHOP_SOURCE_PATH' => $facts->getSourcePath(),
            'VENDOR_PATH' => $facts->getVendorPath(),
            'DB_NAME' => $facts->getDatabaseName(),
            'DB_USERNAME' => $facts->getDatabaseUserName(),
            'DB_PASSWORD' => $facts->getDatabasePassword(),
            'DB_HOST' => $facts->getDatabaseHost(),
            'DB_PORT' => $facts->getDatabasePort(),
            'DUMP_PATH' => $this->getTestDataDumpFilePath(),
            'MODULE_DUMP_PATH' => $this->getModuleTestDataDumpFilePath(),
            'MYSQL_CONFIG_PATH' => $this->generateMysqlStarUpConfigurationFile($facts),
            'PHP_BIN' => $php,
            'FIXTURES_PATH' => $this->getFixturesFilePath()
        ];
    }

    private function getModuleTestDataDumpFilePath(): string
    {
        return Path::join(__DIR__, '..', 'Support', 'Data', 'dump.sql');
    }

    private function getFixturesFilePath(): string
    {
        return Path::join(__DIR__, '..', 'Support', 'Data', 'fixtures.sql');
    }

    private function getTestDataDumpFilePath(): string
    {
        return Path::join(__DIR__, '..', 'Support', '_generated', 'shop-dump.sql');
    }

    private function generateMysqlStarUpConfigurationFile(Facts $facts): string
    {
        return Database::generateStartupOptionsFile(
            user: $facts->getDatabaseUserName(),
            pass: $facts->getDatabasePassword(),
            host: $facts->getDatabaseHost(),
            port: $facts->getDatabasePort()
        );
    }
}


