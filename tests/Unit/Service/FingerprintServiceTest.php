<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;
use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;
use OxidEsales\GraphQL\Base\Service\CookieServiceInterface;
use OxidEsales\GraphQL\Base\Service\FingerprintService;
use OxidEsales\GraphQL\Base\Service\FingerprintServiceInterface;
use PHPUnit\Framework\TestCase;

class FingerprintServiceTest extends TestCase
{
    public function testGetFingerprintGeneratesRandomStrings(): void
    {
        $sut = $this->getSut();

        $result1 = $sut->getFingerprint();
        $result2 = $sut->getFingerprint();

        $this->assertNotSame($result1, $result2);
    }

    public function testGetFingerprintLengthIsAtLeast32(): void
    {
        $sut = $this->getSut();

        $result = $sut->getFingerprint();

        $this->assertTrue(strlen($result) >= 32);
    }

    public function testHashFingerprintReturnsNotEmptyResultOnEmptyParameter(): void
    {
        $sut = $this->getSut();

        $result = $sut->hashFingerprint('');

        $this->assertNotEmpty($result);
    }

    public function testHashFingerprintReturnsTheSameResultOnSameParameter(): void
    {
        $sut = $this->getSut();

        $value = uniqid();
        $result1 = $sut->hashFingerprint($value);
        $result2 = $sut->hashFingerprint($value);

        $this->assertSame($result1, $result2);
    }

    public function testHashFingerprintReturnsHashedVersionOfFingerprint(): void
    {
        $sut = $this->getSut();

        $originalFingerprint = $sut->getFingerprint();
        $hashedFingerprint = $sut->hashFingerprint($originalFingerprint);

        $this->assertNotSame($originalFingerprint, $hashedFingerprint);
    }

    public function testFingerprintValidationOnCorrectData(): void
    {
        $sut = $this->getSut(
            cookieService: $this->createConfiguredStub(CookieServiceInterface::class, [
                'getFingerprintCookie' => $cookieValue = uniqid()
            ])
        );

        $hashedFingerprint = $sut->hashFingerprint($cookieValue);

        $sut->validateFingerprintHashToCookie($hashedFingerprint);
        $this->addToAssertionCount(1);
    }

    public function testFingerprintValidationOnIncorrectData(): void
    {
        $sut = $this->getSut(
            cookieService: $this->createConfiguredStub(CookieServiceInterface::class, [
                'getFingerprintCookie' => uniqid()
            ])
        );

        $hashedWrongFingerprint = $sut->hashFingerprint(uniqid());

        $this->expectException(FingerprintValidationException::class);
        $sut->validateFingerprintHashToCookie($hashedWrongFingerprint);
    }

    public function testFingerprintValidationDoesNotCatchCookieFingerprintMissingException(): void
    {
        $sut = $this->getSut(
            cookieService: $cookieServiceMock = $this->createMock(CookieServiceInterface::class)
        );
        $cookieServiceMock->method('getFingerprintCookie')->willThrowException(new FingerprintMissingException());

        $this->expectException(FingerprintMissingException::class);
        $sut->validateFingerprintHashToCookie(uniqid());
    }

    public function getSut(
        CookieServiceInterface $cookieService = null,
    ): FingerprintServiceInterface {
        return new FingerprintService(
            cookieService: $cookieService ?? $this->createStub(CookieServiceInterface::class)
        );
    }
}
