<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\LoginService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LoginService::class)]
class LoginServiceTest extends TestCase
{
    public function testLoginReturnsUserDataType(): void
    {
        $sut = new LoginService(
            legacyInfrastructure: $legacyInfrastructureMock = $this->createMock(Legacy::class),
        );

        $userName = uniqid();
        $password = uniqid();

        $legacyInfrastructureMock->method('login')
            ->with($userName, $password)
            ->willReturn($userType = $this->createStub(UserInterface::class));

        $this->assertSame($userType, $sut->login($userName, $password));
    }
}
