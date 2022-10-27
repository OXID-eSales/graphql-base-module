<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketPayments;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeveloperBeforeBasketPaymentsEventSubscriber implements EventSubscriberInterface
{
    public function handle(BeforeBasketPayments $event): BeforeBasketPayments
    {
        //get the user basket id from event
        $userBasketId = (string) $event->getBasketId();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketPayments' => 'handle'
        ];
    }
}
