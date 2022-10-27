<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function rand;

class BasketAuthorizationEventSubscriber implements EventSubscriberInterface
{
    public function handleAuth(BasketAuthorization $event): BasketAuthorization {
        // decide
        if (rand(0, 1)) {
            $event->setAuthorized(true);
        }
        return $event;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BasketAuthorization::class => 'handleAuth'
        ];
    }
}
