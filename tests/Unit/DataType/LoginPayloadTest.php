<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use OxidEsales\GraphQL\Base\DataType\LoginPayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(LoginPayload::class)]
class LoginPayloadTest extends AbstractPayloadTestCase
{
    #[Test]
    public function fields(): void
    {
        $loginStub = $this->createStub(LoginInterface::class);
        $sut = new LoginPayload($loginStub);

        $this->assertSame($loginStub, $sut->login());
    }

    #[Test]
    public function nullLogin(): void
    {
        $sut = new LoginPayload(null);

        $this->assertNull($sut->login());
    }

    protected function createPayload(array $userErrors = []): PayloadInterface
    {
        return new LoginPayload(null, $userErrors);
    }
}
