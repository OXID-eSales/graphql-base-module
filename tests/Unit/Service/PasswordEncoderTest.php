<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Service;

use OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker;
use OxidEsales\Eshop\Core\PasswordSaltGenerator;
use OxidEsales\GraphQl\Service\PasswordEncoder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PasswordEncoderTest extends TestCase
{
    public function testPasswordEncoding()
    {
        $encodingService = new PasswordEncoder();
        $saltGenerator = new PasswordSaltGenerator(new OpenSSLFunctionalityChecker());
        $salt = $saltGenerator->generate();
        $hashedPassword = $encodingService->encodePassword("SuperPasswort", $salt);
        $this->assertNotNull($hashedPassword);
        $this->assertTrue(strlen($hashedPassword) >= 64);
    }
}
