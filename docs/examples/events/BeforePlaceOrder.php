<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforePlaceOrder;

class DeveloperBeforePlaceOrderEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handle(BeforePlaceOrder $event): BeforePlaceOrder
    {
        //get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforePlaceOrder' => 'handle'
        ];
    }
}
