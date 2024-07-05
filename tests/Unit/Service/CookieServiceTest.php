<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Service\CookieService;
use OxidEsales\GraphQL\Base\Service\FingerprintServiceInterface;
use PHPUnit\Framework\TestCase;

class CookieServiceTest extends TestCase
{
    public function testSetFingerprintCookieSetsCorrectHeader(): void
    {
        $exampleFingerprint = uniqid();

        $sut = new CookieService();
        $sut->setFingerprintCookie($exampleFingerprint);

        $currentHeaders = xdebug_get_headers();
        $this->assertTrue(in_array(
            sprintf(
                "Set-Cookie: %s=%s; HttpOnly",
                FingerprintServiceInterface::COOKIE_KEY,
                $exampleFingerprint
            ),
            $currentHeaders
        ));
    }
}
