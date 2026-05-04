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
    public function oeLoadFromDb(): void
    {
        $id = uniqid();
        $languageId = rand(0, 3);
        $params = uniqid();
        $expectedUrl = uniqid();

        $sut = $this->createPartialMock(SeoEncoderArticle::class, ['loadFromDb']);
        $sut->method('loadFromDb')
            ->with('oxarticle', $id, $languageId, null, $params)
            ->willReturn($expectedUrl);

        $this->assertSame($expectedUrl, $sut->oeLoadFromDb($id, $languageId, $params));
    }

    #[Test]
    public function oeLoadFromDbWithNullParams(): void
    {
        $id = uniqid();
        $languageId = rand(0, 3);
        $expectedUrl = uniqid();

        $sut = $this->createPartialMock(SeoEncoderArticle::class, ['loadFromDb']);
        $sut->method('loadFromDb')
            ->with('oxarticle', $id, $languageId, null, null)
            ->willReturn($expectedUrl);

        $this->assertSame($expectedUrl, $sut->oeLoadFromDb($id, $languageId));
    }
}
