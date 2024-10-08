<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event\Subscriber;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent;
use OxidEsales\GraphQL\Base\Event\Subscriber\PasswordChangeSubscriber;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepositoryInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Token;
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

    public function testSubscriberWithUserModelPwdChange(): void
    {
        $userModelService = $this->createPartialMock(UserModelService::class, ['isPasswordChanged']);
        $userModelService->method('isPasswordChanged')->with($userId = uniqid())->willReturn(true);

        $userModelStub = $this->createStub(User::class);
        $userModelStub->method('getId')
            ->willReturn($userId);

        $refreshTokenRepository = $this->createMock(RefreshTokenRepositoryInterface::class);
        $refreshTokenRepository->expects($this->once())
            ->method('invalidateUserTokens');

        $tokenInfrastructure = $this->createMock(Token::class);
        $tokenInfrastructure->expects($this->once())
            ->method('invalidateUserTokens');

        $beforeUpdateStub = $this->getBeforeUpdateEvent($userModelStub);
        $afterUpdateStub = $this->getAfterUpdateEvent($userModelStub);

        $sut = $this->getSut($userModelService, $refreshTokenRepository, $tokenInfrastructure);
        $sut->handleBeforeUpdate($beforeUpdateStub);
        $sut->handleAfterUpdate($afterUpdateStub);
    }

    public function testSubscriberWithNoUserModel(): void
    {
        $userModelService = $this->createPartialMock(UserModelService::class, ['isPasswordChanged']);
        $userModelService->expects($this->never())
            ->method('isPasswordChanged');

        $refreshTokenRepository = $this->createMock(RefreshTokenRepositoryInterface::class);
        $refreshTokenRepository->expects($this->never())
            ->method('invalidateUserTokens');

        $tokenInfrastructure = $this->createMock(Token::class);
        $tokenInfrastructure->expects($this->never())
            ->method('invalidateUserTokens');

        $beforeUpdateStub = $this->getBeforeUpdateEvent(new Article());
        $afterUpdateStub = $this->getAfterUpdateEvent(new Article());

        $sut = $this->getSut($userModelService, $refreshTokenRepository, $tokenInfrastructure);
        $sut->handleBeforeUpdate($beforeUpdateStub);
        $sut->handleAfterUpdate($afterUpdateStub);
    }

    public function testSubscriberWithUserModelNoPwdChanged(): void
    {
        $userModelService = $this->createPartialMock(UserModelService::class, ['isPasswordChanged']);
        $userModelService->method('isPasswordChanged')->with($userId = uniqid())->willReturn(false);

        $userModelStub = $this->createStub(User::class);
        $userModelStub->method('getId')
            ->willReturn($userId);

        $refreshTokenRepository = $this->createMock(RefreshTokenRepositoryInterface::class);
        $refreshTokenRepository->expects($this->never())
            ->method('invalidateUserTokens');

        $tokenInfrastructure = $this->createMock(Token::class);
        $tokenInfrastructure->expects($this->never())
            ->method('invalidateUserTokens');

        $beforeUpdateStub = $this->getBeforeUpdateEvent($userModelStub);
        $afterUpdateStub = $this->getAfterUpdateEvent($userModelStub);

        $sut = $this->getSut($userModelService, $refreshTokenRepository, $tokenInfrastructure);
        $sut->handleBeforeUpdate($beforeUpdateStub);
        $sut->handleAfterUpdate($afterUpdateStub);
    }

    protected function getBeforeUpdateEvent(BaseModel $model)
    {
        $beforeUpdateStub = $this->createStub(BeforeModelUpdateEvent::class);
        $beforeUpdateStub->method('getModel')
            ->willReturn($model);

        return $beforeUpdateStub;
    }

    protected function getAfterUpdateEvent(BaseModel $model)
    {
        $afterUpdateStub = $this->createStub(AfterModelUpdateEvent::class);
        $afterUpdateStub->method('getModel')
            ->willReturn($model);

        return $afterUpdateStub;
    }

    protected function getSut(
        UserModelService $userModelService = null,
        RefreshTokenRepositoryInterface $refreshTokenRepository = null,
        Token $tokenInfrastructure = null,
    ): PasswordChangeSubscriber {
        return new PasswordChangeSubscriber(
            userModelService: $userModelService ?? $this->createStub(UserModelService::class),
            refreshTokenRepository:
                $refreshTokenRepository ?? $this->createStub(RefreshTokenRepositoryInterface::class),
            tokenInfrastructure: $tokenInfrastructure ?? $this->createStub(Token::class)
        );
    }
}
