<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Service;

use OxidEsales\Eshop\Application\Model\User as UserModel;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\UserModelService;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserModelService::class)]
class UserModelServiceTest extends TestCase
{
    public function testIsPasswordChangedOnNewPassword(): void
    {
        $userId = uniqid();
        $password = uniqid();

        $user = $this->getUserModelStub($userId, $password);

        $sut = new UserModelService(
            legacyInfrastructure: $this->getLegacyMock($user),
        );

        $this->assertTrue($sut->isPasswordChanged($userId, 'mynewpwd'));
    }

    public function testIsPasswordChangedOnSamePassword(): void
    {
        $userId = uniqid();
        $password = uniqid();

        $user = $this->getUserModelStub($userId, $password);

        $sut = new UserModelService(
            legacyInfrastructure: $this->getLegacyMock($user),
        );

        $this->assertFalse($sut->isPasswordChanged($userId, $password));
    }

    protected function getUserModelStub(string $id, string $password): UserModel
    {
        $userModel = oxNew(UserModel::class);
        $userModel->assign([
            'oxid' => $id,
            'oxpassword' => $password,
        ]);

        return $userModel;
    }

    private function getLegacyMock(UserModel $user): Legacy
    {
        $legacyInfrastructureMock = $this->getMockBuilder(Legacy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $legacyInfrastructureMock->method('getUserModel')
            ->willReturn($user);

        return $legacyInfrastructureMock;
    }
}
