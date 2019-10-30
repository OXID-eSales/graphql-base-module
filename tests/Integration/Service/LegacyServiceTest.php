<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration\Service;

use oxfield;
use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Exception\InvalidLoginException;
use OxidEsales\GraphQL\Service\LegacyService;
use OxidEsales\GraphQL\Service\LegacyServiceInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class LegacyServiceTest extends UnitTestCase
{
    /** @var LegacyService */
    private $legacyService;

    public function setUp()
    {
        parent::setUp();
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $this->legacyService = $container->get(LegacyServiceInterface::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cleanUpTable('oxuser');
    }

    public function testValidLogin()
    {
        $works = false;
        $this->legacyService->checkCredentials('admin', 'admin');
        $works = true;
        $this->assertTrue($works);
    }

    public function testInvalidLogin()
    {
        $this->expectException(InvalidLoginException::class);
        $this->legacyService->checkCredentials('admin', 'wrongpassword');
    }

    public function testGetUsergroupAdmin()
    {
        $this->assertEquals(LegacyServiceInterface::GROUP_ADMIN, $this->legacyService->getUserGroup('admin'));
    }

    public function testGetUserGroupShopadmin()
    {
        $this->createUser('1');
        $this->assertEquals(LegacyServiceInterface::GROUP_ADMIN, $this->legacyService->getUserGroup('testuser'));
    }

    public function testGetUserGroupShopadminWrongShop()
    {
        $this->expectException(InvalidLoginException::class);
        $this->createUser('3');
        $this->legacyService->getUserGroup('testuser');
    }

    public function testGetUserGroupIllegalGroup()
    {
        $this->expectException(InvalidLoginException::class);
        $this->createUser('bla');
        $this->legacyService->getUserGroup('testuser');
    }

    public function testGetUserGroupCustomer()
    {
        $this->createUser('user');
        $this->assertEquals(LegacyServiceInterface::GROUP_CUSTOMERS, $this->legacyService->getUserGroup('testuser'));
    }

    public function testGetUserGroupNotExistingUser()
    {
        $this->expectException(InvalidLoginException::class);
        $this->assertEquals(LegacyServiceInterface::GROUP_CUSTOMERS, $this->legacyService->getUserGroup('testuser'));
    }

    private function createUser($dbusergroup)
    {
        // Needed to get the permissions for setting the user group
        $this->legacyService->checkCredentials('admin', 'admin');

        $oUser = oxNew(User::class);
        $oUser->setId('_testUser');
        $oUser->oxuser__oxusername = new oxField('testuser', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField($dbusergroup, oxField::T_RAW);
        $oUser->createUser();
    }

}