<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketModify;

class DeveloperBeforeBasketModifyEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handle(BeforeBasketModify $event): BeforeBasketModify
    {
        //Get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        /**
         * Gets the type of the event:
         *
         * 0 = TYPE_NOT_SPECIFIED
         * 1 = TYPE_SET_DELIVERY_ADDRESS
         * 2 = TYPE_SET_DELIVERY_METHOD
         * 3 = TYPE_SET_PAYMENT_METHOD
         */
        $type = $event->getEventType();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketModify' => 'handle'
        ];
    }
}
