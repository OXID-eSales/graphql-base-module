<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Service;

use OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker;
use OxidEsales\Eshop\Core\PasswordSaltGenerator;
use OxidEsales\GraphQl\Service\PasswordEncoder;
use OxidEsales\GraphQl\Utility\LegacyWrapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class LegacyWrapperTest extends TestCase
{
    /** @var  LegacyWrapper */
    private $legacyWrapper;

    public function setUp()
    {
        $this->legacyWrapper = new LegacyWrapper(new NullLogger());
    }

    public function testPasswordEncoding()
    {
        $saltGenerator = new PasswordSaltGenerator(new OpenSSLFunctionalityChecker());
        $salt = $saltGenerator->generate();
        $hashedPassword = $this->legacyWrapper->encodePassword("SuperPasswort", $salt);
        $this->assertNotNull($hashedPassword);
        $this->assertTrue(strlen($hashedPassword) >= 64);
    }

    public function testUidGeneration()
    {
        $uid1 = $this->legacyWrapper->createUid();
        $uid2 = $this->legacyWrapper->createUid();

        $this->assertEquals(32, strlen(($uid1)));
        $this->assertEquals(32, strlen(($uid2)));
        $this->assertTrue($uid1 !== $uid2);
    }
}
