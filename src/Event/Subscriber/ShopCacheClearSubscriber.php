<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event\Subscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ShopCacheClearSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheInterface $cache)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function invalidateSchemaCache(ClearShopCacheEvent $event): void
    {
        $this->cache->clear();
    }

    /**
     * @return array<class-string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ClearShopCacheEvent::class => 'invalidateSchemaCache',
        ];
    }
}
