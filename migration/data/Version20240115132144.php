<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240115132144 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        if (!$schema->hasTable('oegraphqlrefreshtoken')) {
            $this->addSql("CREATE TABLE `oegraphqlrefreshtoken` (
                `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci  NOT NULL COMMENT 'Primary oxid',
                `OXSHOPID` int(11) NOT NULL DEFAULT '0' COMMENT 'Shop id (oxshops), value 0 in case no shop was specified',
                `OXUSERID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci  NOT NULL COMMENT 'Userid for this order',
                `TOKEN` char(255) NOT NULL default '' COMMENT 'token string',
                `ISSUED_AT` datetime NOT NULL COMMENT 'creation date',
                `EXPIRES_AT` datetime NOT NULL COMMENT 'expiration date',
                `OXTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp',
                PRIMARY KEY (`OXID`),
                KEY `OXUSERID` (`OXUSERID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }

    public function down(Schema $schema): void
    {
    }
}
