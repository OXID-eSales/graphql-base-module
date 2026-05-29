<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderVendorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderVendorFactory::class)]
final class SeoEncoderVendorFactoryTest extends IntegrationTestCase
{
    #[Test]
    public function createReturnsSeoEncoderVendorAndNewInstance(): void
    {
        $sut = $this->getSut();

        $firstResult = $sut->create();
        $secondResult = $sut->create();

        $this->assertInstanceOf(SeoEncoderVendor::class, $firstResult);
        $this->assertNotSame($firstResult, $secondResult);
    }

    private function getSut(): SeoEncoderVendorFactory
    {
        return new SeoEncoderVendorFactory();
    }
}
