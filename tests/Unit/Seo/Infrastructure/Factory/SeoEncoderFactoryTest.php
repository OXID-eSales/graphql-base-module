<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Application\Model\SeoEncoderContent;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\GraphQL\Base\Seo\Enum\SeoType;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderFactory;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderArticleFactoryInterface;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderCategoryFactoryInterface;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderContentFactoryInterface;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderDefaultFactoryInterface;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderManufacturerFactoryInterface;
use OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo\SeoEncoderVendorFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeoEncoderFactory::class)]
final class SeoEncoderFactoryTest extends TestCase
{
    #[Test]
    #[DataProvider('seoTypeToFactoryProvider')]
    public function createDelegatesToMatchingFactory(
        ?SeoType $seoType,
        string $constructorArgument,
        string $modelClass,
    ): void {
        $expected = $this->createStub($modelClass);
        $factoryInterface = $this->factoryInterfaceFor($constructorArgument);
        $factory = $this->createConfiguredStub($factoryInterface, ['create' => $expected]);

        $sut = $this->getSut(...[$constructorArgument => $factory]);

        $this->assertSame($expected, $sut->create($seoType));
    }

    public static function seoTypeToFactoryProvider(): array
    {
        return [
            'default' => [null, 'defaultFactory', SeoEncoder::class],
            'article' => [SeoType::Article, 'articleFactory', SeoEncoderArticle::class],
            'category' => [SeoType::Category, 'categoryFactory', SeoEncoderCategory::class],
            'manufacturer' => [SeoType::Manufacturer, 'manufacturerFactory', SeoEncoderManufacturer::class],
            'vendor' => [SeoType::Vendor, 'vendorFactory', SeoEncoderVendor::class],
            'content' => [SeoType::Content, 'contentFactory', SeoEncoderContent::class],
        ];
    }

    private function factoryInterfaceFor(string $constructorArgument): string
    {
        return match ($constructorArgument) {
            'defaultFactory' => SeoEncoderDefaultFactoryInterface::class,
            'articleFactory' => SeoEncoderArticleFactoryInterface::class,
            'categoryFactory' => SeoEncoderCategoryFactoryInterface::class,
            'manufacturerFactory' => SeoEncoderManufacturerFactoryInterface::class,
            'vendorFactory' => SeoEncoderVendorFactoryInterface::class,
            'contentFactory' => SeoEncoderContentFactoryInterface::class,
        };
    }

    private function getSut(
        ?SeoEncoderDefaultFactoryInterface $defaultFactory = null,
        ?SeoEncoderArticleFactoryInterface $articleFactory = null,
        ?SeoEncoderCategoryFactoryInterface $categoryFactory = null,
        ?SeoEncoderManufacturerFactoryInterface $manufacturerFactory = null,
        ?SeoEncoderVendorFactoryInterface $vendorFactory = null,
        ?SeoEncoderContentFactoryInterface $contentFactory = null,
    ): SeoEncoderFactory {
        return new SeoEncoderFactory(
            $defaultFactory ?? $this->createStub(SeoEncoderDefaultFactoryInterface::class),
            $articleFactory ?? $this->createStub(SeoEncoderArticleFactoryInterface::class),
            $categoryFactory ?? $this->createStub(SeoEncoderCategoryFactoryInterface::class),
            $manufacturerFactory ?? $this->createStub(SeoEncoderManufacturerFactoryInterface::class),
            $vendorFactory ?? $this->createStub(SeoEncoderVendorFactoryInterface::class),
            $contentFactory ?? $this->createStub(SeoEncoderContentFactoryInterface::class),
        );
    }
}
