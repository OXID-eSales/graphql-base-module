<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Dao;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\Dao\UserDao;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Exception\UserNotFoundException;
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
        $this->expectException(UserNotFoundException::class);

        $this->userDao->verifyPassword("nonexisting user", "password");
    }

    public function testWrongPassword()
    {
        $this->expectException(PasswordMismatchException::class);

        $this->userDao->verifyPassword("admin", "password");
    }

    public function testCorrectVerification()
    {
        $oxid = $this->userDao->verifyPassword("admin", "admin");

        $this->assertEquals('oxdefaultadmin', $oxid);
    }
}
