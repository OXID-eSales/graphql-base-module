<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemove;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeveloperBeforeBasketRemoveEventSubscriber implements EventSubscriberInterface
{
    public function handle(BeforeBasketRemove $event): BeforeBasketRemove
    {
        //get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemove' => 'handle'
        ];
    }
}
