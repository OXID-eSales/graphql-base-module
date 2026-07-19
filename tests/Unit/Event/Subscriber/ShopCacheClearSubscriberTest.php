<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event\Subscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\GraphQL\Base\Event\Subscriber\ShopCacheClearSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

#[CoversClass(ShopCacheClearSubscriber::class)]
class ShopCacheClearSubscriberTest extends TestCase
{
    #[Test]
    public function getSubscribedEventsMapsShopCacheClearToInvalidation(): void
    {
        $this->assertSame(
            [ClearShopCacheEvent::class => 'invalidateSchemaCache'],
            ShopCacheClearSubscriber::getSubscribedEvents()
        );
    }

    #[Test]
    public function invalidateSchemaCacheClearsGraphqlCache(): void
    {
        $cacheSpy = $this->createMock(CacheInterface::class);
        $cacheSpy->expects($this->once())->method('clear');

        $sut = $this->getSut(cache: $cacheSpy);

        $sut->invalidateSchemaCache(new ClearShopCacheEvent(random_int(1, 9999)));
    }

    private function getSut(?CacheInterface $cache = null): ShopCacheClearSubscriber
    {
        return new ShopCacheClearSubscriber(
            cache: $cache ?? $this->createStub(CacheInterface::class)
        );
    }
}
