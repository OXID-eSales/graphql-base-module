<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Exception\NoSignatureKeyException;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
use OxidEsales\GraphQL\Base\Service\LegacyServiceInterface;
use OxidEsales\GraphQL\Base\Service\LegacyService;
use PHPUnit\Framework\TestCase;

class KeyRegistryTest extends TestCase
{

    public function testGenerateSignatureKeyCreatesRandom64BytesKeys()
    {
        $legacyMock = $this->getMockBuilder(LegacyServiceInterface::class)->getMock();
        $keyRegistry = new KeyRegistry($legacyMock);
        $iterations = 5;
        $keys = [];
        for ($i = 0; $i < $iterations; $i++) {
            $key = $keyRegistry->generateSignatureKey();
            $this->assertGreaterThanOrEqual(
                64,
                strlen($key),
                'Signature key needs to be at least 64 chars, ' . strlen($key) . ' chars given'
            );
            $this->assertTrue(is_string($key), 'Signature key needs to be a string');
            $keys[] = $key;
        }
        array_unique($keys);
        $this->assertEquals(
            $iterations,
            count($keys),
            'All signature keys need to be random'
        );
    }

    public function signatureKeyProvider(): array
    {
        return [
            [true, false],
            [null, false],
            [false, false],
            [new \stdClass(), false],
            ['', false],
            ['too short', false],
            [[], false],
            ['33189b36e3fe1198cb92f49c8b6701cfd92bc58876f33361fc97bb69614c9592', true]
        ];
    }

    /**
     * @dataProvider signatureKeyProvider
     */
    public function testGetSignatureKeyWithInvalidOrNoSignature($signature, bool $valid)
    {
        $legacyMock = $this->getMockBuilder(LegacyServiceInterface::class)->getMock();
        $legacyMock->method('getConfigParam')
               ->with(KeyRegistry::SIGNATUREKEYNAME)
               ->willReturn($signature);
        $keyRegistry = new KeyRegistry($legacyMock);
        $e = null;
        $sig = null;
        try {
            $sig = $keyRegistry->getSignatureKey();
        } catch (NoSignatureKeyException $e) {
        }
        if ($valid) {
            $this->assertEquals(null, $e);
            $this->assertTrue(is_string($sig), 'Signature key needs to be a string');
        } else {
            $this->assertInstanceOf(
                NoSignatureKeyException::class,
                $e
            );
        }
    }
}
