<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\Framework\ModuleSetup;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleSetupTest extends TestCase
{
    /** @var  ModuleSetup $dbSetup */
    private $dbSetup;

    public function setUp()
    {
        $this->dbSetup = ContainerFactory::getInstance()->getContainer()->get(ModuleSetup::class);
        parent::setUp();
    }

    public function testForTokenTable()
    {
        $this->assertFalse($this->dbSetup->checkIfTokenTableExists());
    }

    public function testCreateTokenTable()
    {
        $this->dbSetup->createTokenTable();
        $this->assertTrue($this->dbSetup->checkIfTokenTableExists());
    }

}
