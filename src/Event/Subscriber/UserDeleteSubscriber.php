<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event\Subscriber;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelDeleteEvent;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserDeleteSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenInfrastructure $tokenInfrastructure)
    {
    }

    /**
     * Handle ApplicationModelChangedEvent.
     *
     * @param Event $event Event to be handled
     */
    public function handle(Event $event): Event
    {
        /** @phpstan-ignore-next-line method.notFound */
        $model = $event->getModel();

        if (!$model instanceof User) {
            return $event;
        }

        $this->tokenInfrastructure->cleanUpTokens();

        return $event;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array<class-string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterModelDeleteEvent::class => 'handle'
        ];
    }
}
