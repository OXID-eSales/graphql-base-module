<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\GraphQL\Base\Exception\MissingSignatureKey;
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;
use PHPUnit\Framework\TestCase;
use stdClass;

class ModuleConfigurationTest extends TestCase
{
    public function testGenerateSignatureKeyCreatesRandom64BytesKeys(): void
    {
        $moduleSettingBridgeMock = $this->getMockBuilder(ModuleSettingBridgeInterface::class)->getMock();
        $moduleConfiguration = new ModuleConfiguration($moduleSettingBridgeMock);
        $iterations = 5;
        $keys = [];

        for ($i = 0; $i < $iterations; $i++) {
            $key = $moduleConfiguration->generateSignatureKey();
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
            [new stdClass(), false],
            ['', false],
            ['too short', false],
            [[], false],
            ['33189b36e3fe1198cb92f49c8b6701cfd92bc58876f33361fc97bb69614c9592', true],
        ];
    }

    /**
     * @dataProvider signatureKeyProvider
     *
     * @param mixed $signature
     */
    public function testGetSignatureKeyWithInvalidOrNoSignature($signature, bool $valid): void
    {
        $moduleSettingBridgeMock = $this->getMockBuilder(ModuleSettingBridgeInterface::class)->getMock();
        $moduleSettingBridgeMock->method('get')->willReturn($signature);

        $moduleConfiguration = new ModuleConfiguration($moduleSettingBridgeMock);

        if (!$valid) {
            $this->expectException(MissingSignatureKey::class);
        }

        $sig = $moduleConfiguration->getSignatureKey();

        $this->assertTrue(
            is_string($sig),
            'Signature key needs to be a string'
        );
    }

    public function testGetTokenLifetimeDefault(): void
    {
        $moduleSettingBridgeMock = $this->getMockBuilder(ModuleSettingBridgeInterface::class)->getMock();
        $moduleSettingBridgeMock->method('get')->willReturn('asdf');

        $moduleConfiguration = new ModuleConfiguration($moduleSettingBridgeMock);

        $this->assertSame('+8 hours', $moduleConfiguration->getTokenLifeTime());
    }

    public function testGetTokenLifetime(): void
    {
        $moduleSettingBridgeMock = $this->getMockBuilder(ModuleSettingBridgeInterface::class)->getMock();
        $moduleSettingBridgeMock->method('get')->willReturn('24hrs');

        $moduleConfiguration = new ModuleConfiguration($moduleSettingBridgeMock);

        $this->assertSame('+24 hours', $moduleConfiguration->getTokenLifeTime());
    }

    public function testGetUserTokenQuota(): void
    {
        $moduleSettingBridgeMock = $this->getMockBuilder(ModuleSettingBridgeInterface::class)->getMock();
        $moduleSettingBridgeMock->method('get')->willReturn('666');

        $moduleConfiguration = new ModuleConfiguration($moduleSettingBridgeMock);

        $this->assertSame(666, $moduleConfiguration->getUserTokenQuota());
    }
}
