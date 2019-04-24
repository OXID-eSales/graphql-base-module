<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Dao;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\Dao\UserDao;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Address;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Utility\AuthConstants;
use PHPUnit\Framework\TestCase;

class UserDaoTest extends TestCase
{
    /** @var  UserDao */
    private $userDao;

    public function setUp()
    {
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $this->userDao = $container->get(UserDaoInterface::class);
    }

    public function testUserNotExisting()
    {
        $this->expectException(PasswordMismatchException::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setUsername('nonexisting_user');
        $tokenRequest->setPassword('password');
        $tokenRequest->setShopid(1);

        $this->userDao->addIdAndUserGroupToTokenRequest($tokenRequest);
    }

    public function testWrongPassword()
    {
        $this->expectException(PasswordMismatchException::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('password');
        $tokenRequest->setShopid(1);

        $this->userDao->addIdAndUserGroupToTokenRequest($tokenRequest);
    }

    public function testCorrectVerification()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('admin');
        $tokenRequest->setShopid(1);

        $tokenRequest = $this->userDao->addIdAndUserGroupToTokenRequest($tokenRequest);

        $this->assertEquals('oxdefaultadmin', $tokenRequest->getUserid());
        $this->assertEquals(AuthConstants::USER_GROUP_ADMIN, $tokenRequest->getGroup());
    }

    public function testGetUserById()
    {
        $user = $this->userDao->getUserById('oxdefaultadmin');
        $this->assertNotNull($user);
        $this->assertNotNull($user->getAddress());
        $this->assertEquals(AuthConstants::USER_GROUP_ADMIN, $user->getUsergroup());
        $this->assertEquals('admin', $user->getUsername());
    }

    public function testGetMallUserById()
    {
        $user = $this->userDao->getUserById('oxdefaultadmin');
        $this->assertNotNull($user);
        $this->assertNotNull($user->getAddress());
        $this->assertEquals(AuthConstants::USER_GROUP_ADMIN, $user->getUsergroup());
        $this->assertEquals('admin', $user->getUsername());
    }

    public function testGetUserByName()
    {
        $user = $this->userDao->getUserByName('admin', 1);
        $this->assertNotNull($user);
        $this->assertNotNull($user->getAddress());
        $this->assertEquals(AuthConstants::USER_GROUP_ADMIN, $user->getUsergroup());
        $this->assertEquals('oxdefaultadmin', $user->getId());
    }

    public function testGetMallUserByName()
    {
        $user = $this->userDao->getUserByName('admin', 2);
        $this->assertNotNull($user);
        $this->assertNotNull($user->getAddress());
        $this->assertEquals(AuthConstants::USER_GROUP_ADMIN, $user->getUsergroup());
        $this->assertEquals('oxdefaultadmin', $user->getId());
    }

    public function testSaveUser()
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setShopid(1);
        $user->setLastname('User');
        $user->setFirstname('Test');
        $user->setUsergroup(AuthConstants::USER_GROUP_CUSTOMER);
        $address = new Address();
        $address->setCountryshortcut('gb');
        $address->setCity('Bielefeld');
        $address->setAdditionalinfo('Hinterhaus');
        $address->setStreet('Ravensberger Strasse');
        $address->setStreetnr('90a');
        $address->setZip('33607');
        $user->setAddress($address);

        $this->userDao->saveOrUpdateUser($user);

        $loadedUser = $this->userDao->getUserByName('testuser', 1);
        $this->assertTrue(strlen($loadedUser->getId()) > 0);
        $this->assertEquals('Ravensberger Strasse', $loadedUser->getAddress()->getStreet());

    }

    public function testSaveUserForShopAdmins()
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setShopid(2);
        $user->setUsergroup(AuthConstants::USER_GROUP_SHOPADMIN);

        $this->userDao->saveOrUpdateUser($user);

        $loadedUser = $this->userDao->getUserByName('testuser', 2);
        $this->assertTrue(strlen($loadedUser->getId()) > 0);
        $this->assertEquals(AuthConstants::USER_GROUP_SHOPADMIN, $loadedUser->getUsergroup());

    }

    public function testUpdateUser()
    {
        $user = $this->userDao->getUserByName('admin', 2);
        $user->setLastname('Andrer Nachname');

        $this->userDao->saveOrUpdateUser($user);

        $loadedUser = $this->userDao->getUserByName('admin', 1);
        $this->assertEquals('oxdefaultadmin', $loadedUser->getId());
        $this->assertEquals('Andrer Nachname', $loadedUser->getLastname());
    }
}
