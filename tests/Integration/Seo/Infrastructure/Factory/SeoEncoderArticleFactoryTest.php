<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderArticleFactory;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory\SeoEncoderArticleFactoryInterface;
use OxidEsales\GraphQL\Base\Seo\Infrastructure\Model\SeoEncoderArticle as ExtendedSeoEncoderArticle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SeoEncoderArticleFactory::class)]
final class SeoEncoderArticleFactoryTest extends IntegrationTestCase
{
    #[Test]
    public function createReturnsSeoEncoderArticle(): void
    {
        $sut = $this->getSut();

        $this->assertInstanceOf(SeoEncoderArticle::class, $sut->create());
    }

    #[Test]
    public function createProducesDifferentObjectsOnEveryCall(): void
    {
        $sut = $this->getSut();

        $this->assertNotSame($sut->create(), $sut->create());
    }

    #[Test]
    public function oxNewResolvesToOurSeoEncoderArticle(): void
    {
        $result = oxNew(SeoEncoderArticle::class);

        $this->assertInstanceOf(ExtendedSeoEncoderArticle::class, $result);
    }

    private function getSut(): SeoEncoderArticleFactoryInterface
    {
        return new SeoEncoderArticleFactory();
    }
}
