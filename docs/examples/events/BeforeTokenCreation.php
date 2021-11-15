<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Base\Event\BeforeTokenCreation;

class DeveloperBeforeTokenCreationEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handle(BeforeTokenCreation $event): BeforeTokenCreation
    {
        //get the token builder from event
        $tokenBuilder = $event->getBuilder();

        //get the user from event
        $user = $event->getUser();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Base\Event\BeforeTokenCreation' => 'handle'
        ];
    }
}
