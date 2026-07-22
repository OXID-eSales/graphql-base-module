<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenPayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenPayload::class)]
class TokenPayloadTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $tokenString = uniqid();
        $userErrors = [$this->createStub(ErrorInterface::class)];

        $sut = new TokenPayload($tokenString, $userErrors);

        $this->assertSame($tokenString, $sut->token());
        $this->assertSame($userErrors, $sut->userErrors());
    }

    #[Test]
    public function nullToken(): void
    {
        $sut = new TokenPayload(null);

        $this->assertNull($sut->token());
        $this->assertEmpty($sut->userErrors());
    }
}
