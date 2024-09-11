<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event\Subscriber;

use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent;
use OxidEsales\GraphQL\Base\Event\Subscriber\PasswordChangeSubscriber;
use OxidEsales\GraphQL\Base\Service\UserModelService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;

class PasswordChangeSubscriberTest extends BaseTestCase
{
    public function testSubscribedEventsConfiguration(): void
    {
        $sut = $this->getSut();
        $configuration = $sut->getSubscribedEvents();

        $this->assertTrue(array_key_exists(BeforeModelUpdateEvent::class, $configuration));
        $this->assertTrue(array_key_exists(AfterModelUpdateEvent::class, $configuration));
        $this->assertTrue($configuration[BeforeModelUpdateEvent::class] === 'handleBeforeUpdate');
        $this->assertTrue($configuration[AfterModelUpdateEvent::class] === 'handleAfterUpdate');
    }

    public function testHandleBeforeUpdateReturnsOriginalEvent(): void
    {
        $sut = $this->getSut();

        $eventStub = $this->createStub(BeforeModelUpdateEvent::class);
        $this->assertSame($eventStub, $sut->handleBeforeUpdate($eventStub));
    }

    public function testHandleAfterUpdateReturnsOriginalEvent(): void
    {
        $sut = $this->getSut();

        $eventStub = $this->createStub(AfterModelUpdateEvent::class);
        $this->assertSame($eventStub, $sut->handleAfterUpdate($eventStub));
    }

    public function getSut(UserModelService $userModelService = null): PasswordChangeSubscriber
    {
        return new PasswordChangeSubscriber(
            userModelService: $userModelService ?? $this->createStub(UserModelService::class)
        );
    }
}
