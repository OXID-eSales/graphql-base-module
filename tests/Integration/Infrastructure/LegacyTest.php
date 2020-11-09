<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Infrastructure;

use oxField;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\TestingLibrary\UnitTestCase;

class LegacyTest extends UnitTestCase
{
    /** @var Legacy */
    private $legacyInfrastructure;

    public function setUp(): void
    {
        parent::setUp();
        $containerFactory = new TestContainerFactory();
        $container        = $containerFactory->create();
        $container->compile();
        $this->legacyInfrastructure = $container->get(Legacy::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanUpTable('oxuser');
    }

    public function testValidLogin(): void
    {
        $works = false;
        $this->legacyInfrastructure->login('admin', 'admin');
        $works = true;
        $this->assertTrue($works);
    }

    public function testInvalidLogin(): void
    {
        $this->expectException(InvalidLogin::class);
        $this->legacyInfrastructure->login(
            'admin',
            'wrongpassword'
        );
    }

    private function createUser($dbusergroup): void
    {
        // Needed to get the permissions for setting the user group
        $this->legacyService->login('admin', 'admin');

        $oUser = oxNew(User::class);
        $oUser->setId('_testUser');
        $oUser->oxuser__oxusername = new oxField('testuser', oxField::T_RAW);
        $oUser->oxuser__oxrights   = new oxField($dbusergroup, oxField::T_RAW);
        $oUser->createUser();
    }
}
