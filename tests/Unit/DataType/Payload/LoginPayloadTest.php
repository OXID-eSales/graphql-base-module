<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\LoginPayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LoginPayload::class)]
class LoginPayloadTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $loginStub = $this->createStub(LoginInterface::class);
        $userErrors = [$this->createStub(ErrorInterface::class)];

        $sut = new LoginPayload($loginStub, $userErrors);

        $this->assertSame($loginStub, $sut->login());
        $this->assertSame($userErrors, $sut->userErrors());
    }

    #[Test]
    public function nullLogin(): void
    {
        $sut = new LoginPayload(null);

        $this->assertNull($sut->login());
        $this->assertEmpty($sut->userErrors());
    }
}
