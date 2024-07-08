<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Service\FingerprintServiceInterface;
use OxidEsales\GraphQL\Base\Service\HeaderService;
use PHPUnit\Framework\TestCase;

class HeaderServiceTest extends TestCase
{
    public function testCleanHeaders(): void
    {
        $randomFingerprint = uniqid();

        $unfiltered = [
            'invalid-header',
            'X-Powered-By: PHP/8.1.29',
            'Expires: Thu, 19 Nov 1981 08:52:00 GMT',
            'Set-Cookie: language=0; path=/; HttpOnly',
            'Set-Cookie: ' . FingerprintServiceInterface::COOKIE_KEY . '=' . $randomFingerprint . '; HttpOnly',
            'not even a header',
        ];

        $expected = [
            'Set-Cookie: ' . FingerprintServiceInterface::COOKIE_KEY . '=' . $randomFingerprint . '; HttpOnly',
        ];

        $sut = $this->getMockBuilder(HeaderService::class)
            ->onlyMethods(['getHeaders'])
            ->getMock();

        $sut->method('getHeaders')->willReturn($unfiltered);

        $sut->cleanCurrentHeaders();

        $headersAfterClean = xdebug_get_headers();

        $this->assertEquals($expected, $headersAfterClean);
    }
}
