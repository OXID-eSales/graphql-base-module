<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemoveOnPlaceOrder;

class DeveloperBeforeBasketRemoveOnPlaceOrderEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handle(BeforeBasketRemoveOnPlaceOrder $event): BeforeBasketRemoveOnPlaceOrder
    {
        //get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        // decide
        if (rand(0, 1)) {
            $event->setPreserveBasketAfterOrder(true);
        }

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemoveOnPlaceOrder' => 'handle'
        ];
    }
}
