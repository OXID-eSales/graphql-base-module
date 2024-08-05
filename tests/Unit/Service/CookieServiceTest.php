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
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class CookieServiceTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testSetFingerprintCookieSetsCorrectHeaderSameSite(): void
    {
        $exampleFingerprint = uniqid();

        $sut = $this->getSut('sameSite');
        $sut->setFingerprintCookie($exampleFingerprint);

        $currentHeaders = xdebug_get_headers();

        $expectedPattern = sprintf(
            "/Set-Cookie: %s=%s; expires=.+; Max-Age=%d; HttpOnly; SameSite=Lax/",
            FingerprintServiceInterface::COOKIE_KEY,
            $exampleFingerprint,
            CookieService::LIFETIME_SECONDS
        );

        $matches = preg_grep($expectedPattern, $currentHeaders);

        $this->assertNotEmpty($matches, 'Cookie not found in headers');
    }

    #[RunInSeparateProcess]
    public function testSetFingerprintCookieSetsCorrectHeaderCrossSite(): void
    {
        $exampleFingerprint = uniqid();

        $sut = $this->getSut('crossSite');
        $sut->setFingerprintCookie($exampleFingerprint);

        $currentHeaders = xdebug_get_headers();

        $expectedPattern = sprintf(
            "/Set-Cookie: %s=%s; expires=.+; Max-Age=%d; secure; HttpOnly; SameSite=None/",
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

        $sut = $this->getSut();
        $this->assertSame($expectedValue, $sut->getFingerprintCookie());
    }

    public function testGetFingerprintCookieExplodesIfNoFingerprintCookie(): void
    {
        $sut = $this->getSut();

        $this->expectException(FingerprintMissingException::class);
        $sut->getFingerprintCookie();
    }

    protected function getSut(string $option = 'crossSite')
    {
        $configurationMock = $this->createMock(ModuleConfiguration::class);
        $configurationMock->method('getCookieSetting')->willReturn($option);

        return new CookieService($configurationMock);
    }
}
