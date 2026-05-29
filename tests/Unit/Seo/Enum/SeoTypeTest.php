<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Seo\Enum;

use OxidEsales\GraphQL\Base\Seo\Enum\SeoType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeoType::class)]
final class SeoTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('seoTypeBackingValueProvider')]
    public function enumCasesHaveCorrectBackingValues(SeoType $seoType, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $seoType->value);
    }

    public static function seoTypeBackingValueProvider(): array
    {
        return [
            'article' => [SeoType::Article, 'oxarticle'],
            'category' => [SeoType::Category, 'oxcategory'],
            'manufacturer' => [SeoType::Manufacturer, 'oxmanufacturer'],
            'vendor' => [SeoType::Vendor, 'oxvendor'],
            'content' => [SeoType::Content, 'oxcontent'],
        ];
    }
}
