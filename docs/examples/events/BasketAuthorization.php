<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization;
use function rand;

class BasketAuthorizationEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handleAuth(BasketAuthorization $event): BasketAuthorization {
        // decide
        if (rand(0, 1)) {
            $event->setAuthorized(true);
        }
        return $event;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BasketAuthorization::NAME => 'handleAuth'
        ];
    }
}
