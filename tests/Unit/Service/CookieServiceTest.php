<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;
use OxidEsales\GraphQL\Base\Service\CookieService;
use OxidEsales\GraphQL\Base\Service\FingerprintServiceInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class CookieServiceTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testSetFingerprintCookieSetsCorrectHeader(): void
    {
        $exampleFingerprint = uniqid();

        $sut = new CookieService();
        $sut->setFingerprintCookie($exampleFingerprint);

        $currentHeaders = xdebug_get_headers();

        $expectedPattern = sprintf(
            "/Set-Cookie: %s=%s; expires=.+; Max-Age=%d; HttpOnly/",
            FingerprintServiceInterface::COOKIE_KEY,
            $exampleFingerprint,
            CookieService::LIFETIME_SECONDS
        );

        $matches = preg_grep($expectedPattern, $currentHeaders);

        $this->assertNotEmpty($matches, 'Cookie not found in headers');
    }

    #[RunInSeparateProcess]
    public function testGetFingerprintCookieReturnsCorrectValueFromCookies(): void
    {
        $_COOKIE[FingerprintServiceInterface::COOKIE_KEY] = $expectedValue = uniqid();

        $sut = new CookieService();
        $this->assertSame($expectedValue, $sut->getFingerprintCookie());
    }

    public function testGetFingerprintCookieExplodesIfNoFingerprintCookie(): void
    {
        $sut = new CookieService();

        $this->expectException(FingerprintMissingException::class);
        $sut->getFingerprintCookie();
    }
}
