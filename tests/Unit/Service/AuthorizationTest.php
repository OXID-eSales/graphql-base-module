<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use OxidEsales\GraphQL\Base\Framework\NullToken;
use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthorizationTest extends TestCase
{
    public function testIsNotAllowedWithoutPermissionsAndWithoutToken(): void
    {
        $auth = new Authorization(
            [],
            $this->getEventDispatcherMock(),
            new NullToken()
        );

        $this->assertFalse(
            $auth->isAllowed('')
        );
    }

    public function testIsNotAllowedWithoutPermissionsButWithToken(): void
    {
        $auth = new Authorization(
            [],
            $this->getEventDispatcherMock(),
            $this->getTokenMock()
        );

        $this->assertFalse(
            $auth->isAllowed('foo')
        );
    }

    public function testIsNotAllowedWithPermissionsButWithoutToken(): void
    {
        $auth = new Authorization(
            $this->getPermissionMocks(),
            $this->getEventDispatcherMock(),
            new NullToken()
        );

        $this->assertFalse(
            $auth->isAllowed('permission')
        );
    }

    public function testIsAllowedWithPermissionsAndWithToken(): void
    {
        $auth = new Authorization(
            $this->getPermissionMocks(),
            $this->getEventDispatcherMock(),
            $this->getTokenMock()
        );

        $this->assertTrue(
            $auth->isAllowed('permission'),
            'Permission "permission" must be granted to group "group"'
        );
        $this->assertTrue(
            $auth->isAllowed('permission2'),
            'Permission "permission2" must be granted to group "group"'
        );
        $this->assertFalse(
            $auth->isAllowed('permission1'),
            'Permission "permission1" must not be granted to group "group"'
        );
    }

    public function testPositiveOverrideAuthBasedOnEvent(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            BeforeAuthorization::NAME,
            function (BeforeAuthorization $event): void {
                $event->setAuthorized(true);
            }
        );
        $auth = new Authorization(
            $this->getPermissionMocks(),
            $eventDispatcher,
            $this->getTokenMock()
        );

        $this->assertTrue(
            $auth->isAllowed('permission'),
            'Permission "permission" must be granted to group "group"'
        );
        $this->assertTrue(
            $auth->isAllowed('permission2'),
            'Permission "permission2" must be granted to group "group"'
        );
        $this->assertTrue(
            $auth->isAllowed('permission1'),
            'Permission "permission1" must be granted to group "group"'
        );
    }

    public function testNegativeOverrideAuthBasedOnEvent(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            BeforeAuthorization::NAME,
            function (BeforeAuthorization $event): void {
                $event->setAuthorized(false);
            }
        );
        $auth = new Authorization(
            $this->getPermissionMocks(),
            $eventDispatcher,
            $this->getTokenMock()
        );

        $this->assertFalse(
            $auth->isAllowed('permission'),
            'Permission "permission" must not be granted to group "group"'
        );
        $this->assertFalse(
            $auth->isAllowed('permission2'),
            'Permission "permission2" must not be granted to group "group"'
        );
        $this->assertFalse(
            $auth->isAllowed('permission1'),
            'Permission "permission1" must not be granted to group "group"'
        );
    }

    private function getTokenMock(): Token
    {
        $token = $this->getMockBuilder(Token::class)->getMock();
        $token->method('hasClaim')
            ->will($this->returnCallback(
                function ($claim) {
                    if ($claim == Authentication::CLAIM_GROUPS) {
                        return true;
                    }

                    if ($claim == Authentication::CLAIM_USERNAME) {
                        return true;
                    }
                }
            ));
        $token->method('getClaim')
            ->will($this->returnCallback(
                function ($claim) {
                    if ($claim == Authentication::CLAIM_GROUPS) {
                        return ['groups' => 'group'];
                    }

                    if ($claim == Authentication::CLAIM_USERNAME) {
                        return 'testuser';
                    }
                }
            ));

        return $token;
    }

    private function getPermissionMocks(): iterable
    {
        $a = $this->getMockBuilder(PermissionProviderInterface::class)
                  ->getMock();
        $a->method('getPermissions')
          ->willReturn([
              'group'  => ['permission'],
              'group1' => ['permission1'],
          ]);
        $b = $this->getMockBuilder(PermissionProviderInterface::class)
                  ->getMock();
        $b->method('getPermissions')
          ->willReturn([
              'group'     => ['permission2'],
              'group2'    => ['permission2'],
              'developer' => ['all'],
          ]);

        return [
            $a,
            $b,
        ];
    }

    private function getEventDispatcherMock(): EventDispatcherInterface
    {
        return $this->getMockBuilder(EventDispatcherInterface::class)
                    ->getMock();
    }
}
