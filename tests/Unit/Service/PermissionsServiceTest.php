<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Service;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\MissingPermissionException;
use OxidEsales\GraphQl\Service\PermissionsProvider;
use OxidEsales\GraphQl\Service\PermissionsService;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PermissionsServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PermissionsService $permissionsService */
    private $permissionsService;

    public function setUp()
    {

        $permissionsProvider = new PermissionsProvider();
        $permissionsProvider->addPermission("group1", "perm1");
        $permissionsProvider->addPermission("group1", "perm2");
        $permissionsProvider->addPermission("group2", "perm3");
        $this->permissionsService = new PermissionsService();
        $this->permissionsService->addPermissionsProvider($permissionsProvider);

    }

    public function testNoToken()
    {
        $this->setExpectedException(MissingPermissionException::class);
        $this->permissionsService->checkPermission(null, 'perm1');
    }

    public function testNotExistingGroup()
    {
        $this->setExpectedException(MissingPermissionException::class);

        $token = new Token();
        $token->setUserGroup("group3");
        $this->permissionsService->checkPermission($token, 'perm1');

    }

    public function testNoPermissionSingle()
    {
        $this->setExpectedException(MissingPermissionException::class);

        $token = new Token();
        $token->setUserGroup("group2");
        $this->permissionsService->checkPermission($token, 'perm1');

    }

    public function testNoPermissionSeveral()
    {
        $this->setExpectedException(MissingPermissionException::class);

        $token = new Token();
        $token->setUserGroup("group2");
        $this->permissionsService->checkPermission($token, ['perm1', 'perm2']);

    }

    public function testWorkingSingle()
    {
        $token = new Token();
        $token->setUserGroup("group2");
        $this->permissionsService->checkPermission($token, ['perm3']);
        $this->assertTrue(true, 'No exception has been thrown');
    }

    public function testWorkingSeveral()
    {
        $token = new Token();
        $token->setUserGroup("group2");
        $this->permissionsService->checkPermission($token, ['perm1', 'perm2', 'perm3']);
        $this->assertTrue(true, 'No exception has been thrown');
    }
}
