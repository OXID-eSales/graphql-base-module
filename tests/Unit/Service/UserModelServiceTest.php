<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\Eshop\Application\Model\User as UserModel;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\UserModelService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserModelService::class)]
class UserModelServiceTest extends TestCase
{
    public function testIsPasswordChangedOnNewPassword(): void
    {
        $userId = uniqid();
        $password = uniqid();
        $newPassword = uniqid();

        $sut = new UserModelService(
            legacyInfrastructure: $legacyInfrastructureMock = $this->createMock(Legacy::class),
        );

        $legacyInfrastructureMock->method('getUserModel')->with($userId)->willReturn(
            $this->getUserModelMock($password)
        );

        $this->assertTrue($sut->isPasswordChanged($userId, $newPassword));

    }

    public function testIsPasswordChangedOnSamePassword(): void
    {
        $userId = uniqid();
        $password = uniqid();
        $newPassword = $password;

        $sut = new UserModelService(
            legacyInfrastructure: $legacyInfrastructureMock = $this->createMock(Legacy::class),
        );

        $legacyInfrastructureMock->method('getUserModel')->with($userId)->willReturn(
            $this->getUserModelMock($password)
        );

        $this->assertFalse($sut->isPasswordChanged($userId, $newPassword));
    }

    protected function getUserModelMock(string $password): UserModel
    {
        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getFieldData')->willReturnMap([
            ['oxpassword', $password],
        ]);

        return $userModelMock;
    }
}
