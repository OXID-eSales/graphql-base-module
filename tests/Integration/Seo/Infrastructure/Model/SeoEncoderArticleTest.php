<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Model\SeoEncoderArticle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderArticle::class)]
final class SeoEncoderArticleTest extends IntegrationTestCase
{
    #[Test]
    public function getOeCategoryUri(): void
    {
        $languageId = rand(0, 3);
        $expectedUri = uniqid();
        $categoryStub = $this->createStub(Category::class);
        $articleStub = $this->createStub(Article::class);

        $sut = $this->createPartialMock(SeoEncoderArticle::class, ['createArticleCategoryUri']);
        $sut->method('createArticleCategoryUri')
            ->with($articleStub, $categoryStub, $languageId)
            ->willReturn($expectedUri);

        $this->assertSame($expectedUri, $sut->oeGetCategoryUri($articleStub, $categoryStub, $languageId));
    }

    #[Test]
    public function isOeFixed(): void
    {
        $objectId = uniqid();
        $languageId = rand(0, 3);
        $params = uniqid();

        $sut = $this->createPartialMock(SeoEncoderArticle::class, ['isFixed']);
        $sut->method('isFixed')
            ->with('oxarticle', $objectId, $languageId, null, $params)
            ->willReturn($isFixed = (bool)rand(0, 1));

        $this->assertSame($isFixed, $sut->oeIsFixed($objectId, $languageId, $params));
    }

    #[Test]
    public function isOeFixedWithNullAsParams(): void
    {
        $objectId = uniqid();
        $languageId = rand(0, 3);

        $sut = $this->createPartialMock(SeoEncoderArticle::class, ['isFixed']);
        $sut->method('isFixed')
            ->with('oxarticle', $objectId, $languageId, null, null)
            ->willReturn($isFixed = (bool)rand(0, 1));

        $this->assertSame($isFixed, $sut->oeIsFixed($objectId, $languageId));
    }
}
