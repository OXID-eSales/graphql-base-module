<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Framework\PermissionProviderInterface;
use OxidEsales\GraphQL\Service\AuthenticationService;
use OxidEsales\GraphQL\Service\AuthorizationService;
# use PHPUnit\Framework\TestCase;
use OxidEsales\TestingLibrary\UnitTestCase as TestCase;

class AuthorizationServiceTest extends TestCase
{
    private function getTokenMock(): Token
    {
        $token = $this->getMockBuilder(Token::class)->getMock();
        $token->method('getClaim')
            ->will($this->returnCallback(
                function ($claim) {
                    if ($claim == AuthenticationService::CLAIM_GROUP) { return "group"; };
                    if ($claim == AuthenticationService::CLAIM_USERNAME) { return "testuser"; };
                }
            ));
        return $token;
    }

    private function getPermissionMocks(): iterable
    {
        $a = $this->getMockBuilder(PermissionProviderInterface::class)->getMock();
        $a->method('getPermissions')
          ->willReturn([
              'group' => ['permission'],
              'group1' => ['permission1']
          ]);
        $b = $this->getMockBuilder(PermissionProviderInterface::class)->getMock();
        $b->method('getPermissions')
          ->willReturn([
              'group' => ['permission2'],
              'group2' => ['permission2'],
              'developer' => ['all']
          ]);
        return [
            $a,
            $b
        ];
    }

    public function testIsNotAllowedWithoutPermissionsAndWithoutToken()
    {
        $auth = new AuthorizationService([]);
        $this->assertFalse($auth->isAllowed(''));
    }

    public function testIsNotAllowedWithoutPermissionsButWithToken()
    {
        $auth = new AuthorizationService([]);
        $auth->setToken(
            $this->getTokenMock()
        );
        $this->assertFalse($auth->isAllowed('foo'));
    }

    public function testIsNotAllowedWithPermissionsButWithoutToken()
    {
        $auth = new AuthorizationService(
            $this->getPermissionMocks()
        );
        $this->assertFalse($auth->isAllowed('permission'));
    }

    public function testIsAllowedWithPermissionsAndWithToken()
    {
        $auth = new AuthorizationService(
            $this->getPermissionMocks()
        );
        $auth->setToken(
            $this->getTokenMock()
        );
        $this->assertTrue($auth->isAllowed('permission'), 'Permission "permission" must be granted to group "group"');
        $this->assertTrue($auth->isAllowed('permission2'), 'Permission "permission2" must be granted to group "group"');
        $this->assertFalse($auth->isAllowed('permission1'), 'Permission "permission1" must not be granted to group "group"');
    }

    public function testIsAllowedWithDeveloperToken()
    {
        $token = $this->getMockBuilder(Token::class)->getMock();
        $token->method('getClaim')
            ->will($this->returnCallback(
                function ($claim) {
                    if ($claim == AuthenticationService::CLAIM_GROUP) { return "group"; };
                    if ($claim == AuthenticationService::CLAIM_USERNAME) { return "developer"; };
                }
            ));
        $auth = new AuthorizationService(
            $this->getPermissionMocks()
        );
        $auth->setToken(
            $token
        );
        $this->assertTrue($auth->isAllowed('anypermission'), 'Developer should have any permission');
    }

    public function testIsNotAllowedWithDeveloperToken()
    {
        $token = $this->getMockBuilder(Token::class)->getMock();
        $token->method('getClaim')
            ->will($this->returnCallback(
                function ($claim) {
                    if ($claim == AuthenticationService::CLAIM_GROUP) { return "group"; };
                    if ($claim == AuthenticationService::CLAIM_USERNAME) { return "developer"; };
                }
            ));
        $auth = new AuthorizationService([]);
        $this->assertFalse($auth->isAllowed('permission'), 'Developer shouldn\'t have any permission');
    }
}
