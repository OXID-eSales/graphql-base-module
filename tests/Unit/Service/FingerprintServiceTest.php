<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintHashNotValidException;
use OxidEsales\GraphQL\Base\Service\FingerprintService;
use PHPUnit\Framework\TestCase;

class FingerprintServiceTest extends TestCase
{
    public function testGetFingerprintGeneratesRandomStrings(): void
    {
        $sut = new FingerprintService();

        $result1 = $sut->getFingerprint();
        $result2 = $sut->getFingerprint();

        $this->assertNotSame($result1, $result2);
    }

    public function testGetFingerprintLengthIsAtLeast32(): void
    {
        $sut = new FingerprintService();

        $result = $sut->getFingerprint();

        $this->assertTrue(strlen($result) >= 32);
    }

    public function testHashFingerprintReturnsNotEmptyResultOnEmptyParameter(): void
    {
        $sut = new FingerprintService();

        $result = $sut->hashFingerprint('');

        $this->assertNotEmpty($result);
    }

    public function testHashFingerprintReturnsTheSameResultOnSameParameter(): void
    {
        $sut = new FingerprintService();

        $value = uniqid();
        $result1 = $sut->hashFingerprint($value);
        $result2 = $sut->hashFingerprint($value);

        $this->assertSame($result1, $result2);
    }

    public function testHashFingerprintReturnsHashedVersionOfFingerprint(): void
    {
        $sut = new FingerprintService();

        $originalFingerprint = $sut->getFingerprint();
        $hashedFingerprint = $sut->hashFingerprint($originalFingerprint);

        $this->assertNotSame($originalFingerprint, $hashedFingerprint);
    }

    public function testFingerprintValidationOnCorrectData(): void
    {
        $sut = new FingerprintService();

        $originalFingerprint = $sut->getFingerprint();
        $hashedFingerprint = $sut->hashFingerprint($originalFingerprint);

        $sut->validateFingerprintHash($originalFingerprint, $hashedFingerprint);
        $this->addToAssertionCount(1);
    }

    public function testFingerprintValidationOnIncorrectData(): void
    {
        $sut = new FingerprintService();

        $originalFingerprint = $sut->getFingerprint();
        $differentFingerpintHash = $sut->hashFingerprint($sut->getFingerprint());

        $this->expectException(FingerprintHashNotValidException::class);
        $sut->validateFingerprintHash($originalFingerprint, $differentFingerpintHash);
    }
}
