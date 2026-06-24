<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Codeception\PHPUnit\TestCase;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\TokenPayload;
use OxidEsales\GraphQL\Base\DataType\TokenPayloadInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TokenPayload::class)]
class TokenPayloadTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $tokenString = uniqid();
        $errorStub = $this->createStub(ErrorInterface::class);

        $sut = new TokenPayload($tokenString, [$errorStub]);

        $this->assertInstanceOf(TokenPayloadInterface::class, $sut);
        $this->assertSame($tokenString, $sut->token());
        $this->assertSame([$errorStub], $sut->userErrors());
    }

    #[Test]
    public function nullToken(): void
    {
        $sut = new TokenPayload(null);

        $this->assertNull($sut->token());
    }
}
