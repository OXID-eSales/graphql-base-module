<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderContent;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderContentFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderContentFactory::class)]
final class SeoEncoderContentFactoryTest extends IntegrationTestCase
{
    #[Test]
    public function createReturnsSeoEncoderContentAndNewInstance(): void
    {
        $sut = $this->getSut();

        $firstResult = $sut->create();
        $secondResult = $sut->create();

        $this->assertInstanceOf(SeoEncoderContent::class, $firstResult);
        $this->assertNotSame($firstResult, $secondResult);
    }

    private function getSut(): SeoEncoderContentFactory
    {
        return new SeoEncoderContentFactory();
    }
}
