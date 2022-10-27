<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeAddItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeveloperBeforeAddItemEventSubscriber implements EventSubscriberInterface
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
