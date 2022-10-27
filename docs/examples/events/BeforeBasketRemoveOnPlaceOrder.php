<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemoveOnPlaceOrder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeveloperBeforeBasketRemoveOnPlaceOrderEventSubscriber implements EventSubscriberInterface
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
