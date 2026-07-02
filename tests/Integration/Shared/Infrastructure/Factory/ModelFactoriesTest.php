<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Shared\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Application\Model\SeoEncoderContent;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Infrastructure\Model\RefreshToken;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\RefreshTokenModelFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderArticleFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderCategoryFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderContentFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderDefaultFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderManufacturerFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderVendorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(RefreshTokenModelFactory::class)]
#[CoversClass(SeoEncoderDefaultFactory::class)]
#[CoversClass(SeoEncoderArticleFactory::class)]
#[CoversClass(SeoEncoderCategoryFactory::class)]
#[CoversClass(SeoEncoderManufacturerFactory::class)]
#[CoversClass(SeoEncoderVendorFactory::class)]
#[CoversClass(SeoEncoderContentFactory::class)]
final class ModelFactoriesTest extends IntegrationTestCase
{
    #[Test]
    #[DataProvider('factoryToModelProvider')]
    public function create(string $factoryClass, string $modelClass): void
    {
        $sut = new $factoryClass();

        $firstResult = $sut->create();
        $secondResult = $sut->create();

        $this->assertInstanceOf($modelClass, $firstResult);
        $this->assertNotSame($firstResult, $secondResult);
    }

    public static function factoryToModelProvider(): array
    {
        return [
            'refreshToken' => [RefreshTokenModelFactory::class, RefreshToken::class],
            'seoEncoderDefault' => [SeoEncoderDefaultFactory::class, SeoEncoder::class],
            'seoEncoderArticle' => [SeoEncoderArticleFactory::class, SeoEncoderArticle::class],
            'seoEncoderCategory' => [SeoEncoderCategoryFactory::class, SeoEncoderCategory::class],
            'seoEncoderManufacturer' => [SeoEncoderManufacturerFactory::class, SeoEncoderManufacturer::class],
            'seoEncoderVendor' => [SeoEncoderVendorFactory::class, SeoEncoderVendor::class],
            'seoEncoderContent' => [SeoEncoderContentFactory::class, SeoEncoderContent::class],
        ];
    }
}
