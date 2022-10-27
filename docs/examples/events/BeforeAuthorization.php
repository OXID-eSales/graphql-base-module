<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\Events;

use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function rand;

class AuthorizationEventSubscriber implements EventSubscriberInterface
{
    public function handleAuth(BeforeAuthorization $event): BeforeAuthorization {
        // decide
        if (rand(0, 1)) {
            $event->setAuthorized(true);
        }
        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeAuthorization::class => 'handleAuth'
        ];
    }
}
