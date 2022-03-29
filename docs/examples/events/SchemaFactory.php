<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Base\Event\SchemaFactory;

class DeveloperSchemaFactoryEventSubscriber extends AbstractShopAwareEventSubscriber
{
    public function handle(SchemaFactory $event): SchemaFactory
    {
        //get the schema factory from event
        $factory = $event->getSchemaFactory();

        //do something

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Base\Event\SchemaFactory' => 'handle'
        ];
    }
}
