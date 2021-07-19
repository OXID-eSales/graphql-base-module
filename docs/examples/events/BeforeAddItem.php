<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeAddItem;

class DeveloperBeforeAddItemEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handle(BeforeAddItem $event): BeforeAddItem
    {
        //get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        //get the product id from event
        $productId = (string) $event->getProductId();

        //get the user basket item amount from event
        $amount = (float) $event->getAmount();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforeAddItem' => 'handle'
        ];
    }
}
