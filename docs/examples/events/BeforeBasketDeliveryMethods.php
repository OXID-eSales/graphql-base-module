<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeveloperBeforeBasketDeliveryMethodsEventSubscriber implements EventSubscriberInterface
{
    public function handle(BeforeBasketDeliveryMethods $event): BeforeBasketDeliveryMethods
    {
        //get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods' => 'handle'
        ];
    }
}
