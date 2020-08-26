<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\Events;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use function rand;

class AuthorizationEventSubscriber extends AbstractShopAwareEventSubscriber
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
            BeforeAuthorization::NAME => 'handleAuth'
        ];
    }
}
