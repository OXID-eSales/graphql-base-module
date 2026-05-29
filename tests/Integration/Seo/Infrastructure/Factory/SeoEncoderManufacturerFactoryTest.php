<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderManufacturerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderManufacturerFactory::class)]
final class SeoEncoderManufacturerFactoryTest extends IntegrationTestCase
{
    #[Test]
    public function createReturnsSeoEncoderManufacturerAndNewInstance(): void
    {
        $sut = $this->getSut();

        $firstResult = $sut->create();
        $secondResult = $sut->create();

        $this->assertInstanceOf(SeoEncoderManufacturer::class, $firstResult);
        $this->assertNotSame($firstResult, $secondResult);
    }

    private function getSut(): SeoEncoderManufacturerFactory
    {
        return new SeoEncoderManufacturerFactory();
    }
}
