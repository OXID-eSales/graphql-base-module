<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeveloperAfterAddItemEventSubscriber implements EventSubscriberInterface
{
    public function handle(AfterAddItem $event): AfterAddItem
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
            'OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem' => 'handle'
        ];
    }
}
