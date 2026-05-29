<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Application\Model\SeoEncoderContent;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Enum\SeoType;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderArticleFactoryInterface;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderCategoryFactoryInterface;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderContentFactoryInterface;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderFactory;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderFactoryInterface;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderManufacturerFactoryInterface;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderVendorFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderFactory::class)]
final class SeoEncoderFactoryTest extends IntegrationTestCase
{
    #[Test]
    public function createWithoutTypeReturnsSeoEncoder(): void
    {
        $result = $this->getSut()->create();

        $this->assertInstanceOf(SeoEncoder::class, $result);
    }

    #[Test]
    #[DataProvider('seoTypeToExpectedClassProvider')]
    public function createReturnsCorrectSeoEncoderTypeAndNewInstance(SeoType $seoType, string $expectedClass): void
    {
        $sut = $this->getSut();

        $firstResult = $sut->create($seoType);
        $secondResult = $sut->create($seoType);

        $this->assertInstanceOf($expectedClass, $firstResult);
        $this->assertNotSame($firstResult, $secondResult);
    }

    public static function seoTypeToExpectedClassProvider(): array
    {
        return [
            'article' => [SeoType::Article, SeoEncoderArticle::class],
            'category' => [SeoType::Category, SeoEncoderCategory::class],
            'manufacturer' => [SeoType::Manufacturer, SeoEncoderManufacturer::class],
            'vendor' => [SeoType::Vendor, SeoEncoderVendor::class],
            'content' => [SeoType::Content, SeoEncoderContent::class],
        ];
    }

    private function getSut(): SeoEncoderFactoryInterface
    {
        return new SeoEncoderFactory(
            $this->get(SeoEncoderArticleFactoryInterface::class),
            $this->get(SeoEncoderCategoryFactoryInterface::class),
            $this->get(SeoEncoderManufacturerFactoryInterface::class),
            $this->get(SeoEncoderVendorFactoryInterface::class),
            $this->get(SeoEncoderContentFactoryInterface::class),
        );
    }
}
