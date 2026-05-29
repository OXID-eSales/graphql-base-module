<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderCategoryFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderCategoryFactory::class)]
final class SeoEncoderCategoryFactoryTest extends IntegrationTestCase
{
    #[Test]
    public function createReturnsSeoEncoderCategoryAndNewInstance(): void
    {
        $sut = $this->getSut();

        $firstResult = $sut->create();
        $secondResult = $sut->create();

        $this->assertInstanceOf(SeoEncoderCategory::class, $firstResult);
        $this->assertNotSame($firstResult, $secondResult);
    }

    private function getSut(): SeoEncoderCategoryFactory
    {
        return new SeoEncoderCategoryFactory();
    }
}
